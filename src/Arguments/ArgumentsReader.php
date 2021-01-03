<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments;

use Doctrine\Instantiator\Instantiator;
use GoetasWebservices\SoapServices\Metadata\SerializerUtils;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Serializer;

class ArgumentsReader implements ArgumentsReaderInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param array $args
     * @param array $message
     */
    public function readArguments(array $args, array $message): object
    {
        $envelopes = array_filter($args, static function ($item) use ($message) {
            return $item instanceof $message['message_fqcn'];
        });
        if ($envelopes) {
            $envelope = reset($envelopes);
            $this->handleHeaders($args, $message, $envelope);

            return $envelope;
        }

        $instantiator = new Instantiator();
        $envelope = $instantiator->instantiate($message['message_fqcn']);
        $this->handleHeaders($args, $message, $envelope);

        if (!count($message['parts'])) {
            return $envelope;
        }

        if ($args[0] instanceof $message['part_fqcn']) {
            $envelope->setBody($args[0]);

            return $envelope;
        }

        $body = $instantiator->instantiate($message['part_fqcn']);
        $envelope->setBody($body);

        $factory = SerializerUtils::getMetadataFactory($this->serializer);

        $classMetadata = $factory->getMetadataForClass($message['part_fqcn']);

        if (count($message['parts']) > 1) {
            if (count($message['parts']) !== count($args)) {
                throw new \Exception('Expected to have exactly ' . count($message['parts']) . ' arguments, supplied ' . count($args));
            }

            foreach ($message['parts'] as $paramName => $elementName) {
                $propertyMetadata = $classMetadata->propertyMetadata[$paramName];
                $this->setValue($body, array_shift($args), $propertyMetadata);
            }

            return $envelope;
        }

        $propertyName = key($message['parts']);
        $propertyMetadata = $classMetadata->propertyMetadata[$propertyName];

        if ($args[0] instanceof $propertyMetadata->type['name']) {
            $this->setValue($body, reset($args), $propertyMetadata);

            return $envelope;
        }

        $instance2 = $instantiator->instantiate($propertyMetadata->type['name']);
        $classMetadata2 = $factory->getMetadataForClass($propertyMetadata->type['name']);
        $this->setValue($body, $instance2, $propertyMetadata);

        foreach ($classMetadata2->propertyMetadata as $propertyMetadata2) {
            if (!count($args)) {
                throw new \Exception("Not enough arguments provided. Can't find a parameter to set " . $propertyMetadata2->name);
            }

            $value = array_shift($args);
            $this->setValue($instance2, $value, $propertyMetadata2);
        }

        return $envelope;
    }

    /**
     * @param array $args
     * @param array $message
     */
    private function handleHeaders(array $args, array $message, object $envelope): void
    {
        $headers = array_filter($args, static function ($item) use ($message) {
            return $item instanceof $message['headers_fqcn'];
        });
        if (count($headers)) {
            $envelope->setHeader(reset($headers));
        }
    }

    /**
     * @param mixed $value
     */
    private function setValue(object $target, $value, PropertyMetadata $propertyMetadata): void
    {
        $context = DeserializationContext::create();
        $accessor = new DefaultAccessorStrategy();

        $accessor->setValue($target, $value, $propertyMetadata, $context);
    }
}
