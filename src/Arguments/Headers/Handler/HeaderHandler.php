<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments\Headers\Handler;

use GoetasWebservices\SoapServices\Metadata\Arguments\Headers\Header;
use GoetasWebservices\SoapServices\Metadata\Arguments\Headers\HeaderBag;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

class HeaderHandler implements SubscribingHandlerInterface
{
    public const SOAP = 'http://schemas.xmlsoap.org/soap/envelope/';
    public const SOAP_12 = 'http://www.w3.org/2003/05/soap-envelope';

    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => HeaderPlaceholder::class,
                'method' => 'serializeHeaderPlaceholder',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => HeaderPlaceholder::class,
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

    /**
     * @return mixed
     */
    public function deserializeHeaderPlaceholder(XmlDeserializationVisitor $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context)
    {
        $type = ['name' => $type['params'][0], 'params' => []];

        return $context->getNavigator()->accept($data, $type, $context);
    }

    /**
     * @return mixed
     */
    public function deserializeHeader(XmlDeserializationVisitor $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context)
    {
        $type = ['name' => $type['params'][0], 'params' => []];

        $return = $context->getNavigator()->accept($data, $type, $context);

        $mustUnderstandAttr = $data->attributes(self::SOAP_12)->mustUnderstand ?: $data->attributes(self::SOAP)->mustUnderstand;
        $mustUnderstand = null !== $mustUnderstandAttr && $visitor->visitBoolean($mustUnderstandAttr, [], $context);
        $headerBag = $context->getAttribute('headers_bag');
        \assert($headerBag instanceof HeaderBag);

        if ($mustUnderstand) {
            $headerBag->addMustUnderstandHeader($return);
        } else {
            $headerBag->addHeader($return);
        }

        return $return;
    }

    public function serializeHeader(XmlSerializationVisitor $visitor, Header $header, array $type, SerializationContext $context): void
    {
        $factory = $context->getMetadataFactory();

        /**
         * @var $classMetadata \JMS\Serializer\Metadata\ClassMetadata
         */
        $classMetadata = $factory->getMetadataForClass(get_class($header->getData()));

        $name = false !== ($pos = strpos($classMetadata->xmlRootName, ':')) ? substr($classMetadata->xmlRootName, $pos + 1) : $classMetadata->xmlRootName;

        $metadata = new StaticPropertyMetadata($classMetadata->name, $name, $header->getData());
        $metadata->xmlNamespace = $classMetadata->xmlRootNamespace;
        $metadata->serializedName = $name;

        $visitor->visitProperty($metadata, $header->getData(), $context);

        $this->handleOptions($visitor, $header->getOptions());
    }

    private function handleOptions(XmlSerializationVisitor $visitor, array $options): void
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
                if (self::SOAP_12 === $currentNode->ownerDocument->documentElement->namespaceURI) {
                    $envelopeNS = self::SOAP_12;
                } else {
                    $envelopeNS = self::SOAP;
                }

                $this->setAttributeOnNode($currentNode->lastChild, $option, $value, $envelopeNS);
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
}
