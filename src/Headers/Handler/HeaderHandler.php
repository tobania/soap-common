<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Headers\Handler;

use GoetasWebservices\SoapServices\Metadata\Envelope\Envelope;
use GoetasWebservices\SoapServices\Metadata\Headers\Header;
use GoetasWebservices\SoapServices\Metadata\Headers\HeadersIncoming;
use GoetasWebservices\SoapServices\Metadata\Headers\HeadersOutgoing;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

class HeaderHandler implements SubscribingHandlerInterface, EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.pre_serialize', 'method' => 'ensureHeaderSerialized', 'interface' => Envelope::class],
        ];
    }

    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'GoetasWebservices\SoapServices\Metadata\Headers\RawFaultDetail',
                'method' => 'deserializeFaultDetail',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => 'GoetasWebservices\SoapServices\Metadata\Headers\Handler\HeaderPlaceholder',
                'method' => 'serializeHeaderPlaceholder',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'GoetasWebservices\SoapServices\Metadata\Headers\Handler\HeaderPlaceholder',
                'method' => 'deserializeHeaderPlaceholder',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'GoetasWebservices\SoapServices\SoapEnvelope\Header',
                'method' => 'deserializeHeader',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => Header::class,
                'method' => 'serializeHeader',
            ],
        ];
    }

    public function ensureHeaderSerialized(PreSerializeEvent $event): void
    {
        $envelope = $event->getObject();
        if (!($envelope instanceof Envelope)) {
            return;
        }

        $context = $event->getContext();
        $headerBag = $context->getAttribute('headers_outgoing');
        \assert($headerBag instanceof HeadersOutgoing);

        if (count($headerBag->getHeaders()) > 0 && !$envelope->getHeader()) {
            $factory = $event->getContext()->getMetadataFactory();
            $envelopeMetadata = $factory->getMetadataForClass($event->getType()['name']);
            $headerType = $envelopeMetadata->propertyMetadata['header']->type;
            $defaultHeaderClass = $headerType['params'][0];
            $envelope->setHeader(new $defaultHeaderClass());
        }
    }

    /**
     * @return mixed
     */
    public function deserializeHeaderPlaceholder(DeserializationVisitorInterface $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context)
    {
        $type = ['name' => $type['params'][0], 'params' => []];
        $headers = $context->getNavigator()->accept($data, $type, $context);

        $headerBag = $context->getAttribute('headers_incoming');
        \assert($headerBag instanceof HeadersIncoming);

        foreach ($data->xpath('./*') as $node) {
            if ($this->isMustUnderstand($node, $visitor, $context)) {
                $headerBag->addMustUnderstandHeader($node);
            } else {
                $headerBag->addHeader($node);
            }
        }

        return $headers;
    }

    /**
     * @return mixed
     */
    public function deserializeHeader(DeserializationVisitorInterface $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context)
    {
        $type = ['name' => $type['params'][0], 'params' => []];

        $return = $context->getNavigator()->accept($data, $type, $context);

        $headerBag = $context->getAttribute('headers_incoming');
        \assert($headerBag instanceof HeadersIncoming);

        if ($this->isMustUnderstand($data, $visitor, $context)) {
            $headerBag->addMustUnderstandHeader($data, $return);
        } else {
            $headerBag->addHeader($data, $return);
        }

        return $return;
    }

    private function isMustUnderstand(\SimpleXMLElement $data, DeserializationVisitorInterface $visitor, DeserializationContext $context): bool
    {
        $domElement = dom_import_simplexml($data);
        $mustUnderstandAttr = $domElement->getAttributeNS($domElement->ownerDocument->documentElement->namespaceURI, 'mustUnderstand');

        return !empty($mustUnderstandAttr) && $visitor->visitBoolean($mustUnderstandAttr, [], $context);
    }

    public function serializeHeaderPlaceholder(SerializationVisitorInterface $visitor, object $data, array $type, SerializationContext $context): void
    {
        // serialize default headers
        $context->stopVisiting($data);
        $type = ['name' => $type['params'][0], 'params' => []];
        $context->getNavigator()->accept($data, $type, $context);
        $context->startVisiting($data);

        // serialize additional headers
        $headerBag = $context->getAttribute('headers_outgoing');
        \assert($headerBag instanceof HeadersOutgoing);

        $factory = $context->getMetadataFactory();
        foreach ($headerBag->getHeaders() as $header) {
            $classMetadata = $factory->getMetadataForClass(get_class($header->getData()));

            $name = false !== ($pos = strpos($classMetadata->xmlRootName, ':')) ? substr($classMetadata->xmlRootName, $pos + 1) : $classMetadata->xmlRootName;

            $metadata = new StaticPropertyMetadata($classMetadata->name, $name, $header->getData());
            $metadata->xmlNamespace = $classMetadata->xmlRootNamespace;
            $metadata->serializedName = $name;

            $visitor->visitProperty($metadata, $header->getData(), $context);

            $this->handleOptions($visitor, $header->getOptions());
        }
    }

    public function serializeHeader(SerializationVisitorInterface $visitor, Header $header, array $type, SerializationContext $context): void
    {
        $data = $header->getData();
        if ($data instanceof \DOMElement) {
            $importedNode = $data->ownerDocument !== $visitor->getDocument()
                ? $visitor->getDocument()->importNode($data, true)
                : $data;
            $visitor->getCurrentNode()->appendChild($importedNode);
        } else {
            $factory = $context->getMetadataFactory();
            /**
             * @var $classMetadata \JMS\Serializer\Metadata\ClassMetadata
             */
            $classMetadata = $factory->getMetadataForClass(get_class($header->getData()));

            $name = false !== ($pos = strpos($classMetadata->xmlRootName, ':')) ? substr($classMetadata->xmlRootName, $pos + 1) : $classMetadata->xmlRootName;

            $metadata = new StaticPropertyMetadata($classMetadata->name, $name, $header->getData());
            $metadata->xmlNamespace = $classMetadata->xmlRootNamespace;
            $metadata->serializedName = $name;

            $visitor->visitProperty($metadata, $data, $context);

            $this->handleOptions($visitor, $header->getOptions());
        }
    }

    private function handleOptions(SerializationVisitorInterface $visitor, array $options): void
    {
        if (!count($options)) {
            return;
        }

        /**
         * @var $currentNode \DOMNode
         */
        $currentNode = $visitor->getCurrentNode();
        foreach ($options as $option => $value) {
            if (in_array($option, ['mustUnderstand', 'required', 'role', 'actor'])) {
                $this->setAttributeOnNode($currentNode->lastChild, $option, $value, $currentNode->ownerDocument->documentElement->namespaceURI);
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function setAttributeOnNode(\DOMElement $node, string $name, $value, string $namespace): void
    {
        if (!($prefix = $node->lookupPrefix($namespace)) && !($prefix = $node->ownerDocument->lookupPrefix($namespace))) {
            $prefix = 'ns-' . substr(sha1($namespace), 0, 8);
        }

        $node->setAttributeNS($namespace, $prefix . ':' . $name, is_bool($value) || null === $value ? ($value ? 'true' : 'false') : $value);
    }

    public function deserializeFaultDetail(DeserializationVisitorInterface $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context): \SimpleXMLElement
    {
        return $data->children();
    }
}
