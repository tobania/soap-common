<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments\Headers\Handler;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\XmlDeserializationVisitor;

class FaultHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods(): array
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'xml',
                'type' => 'GoetasWebservices\SoapServices\Metadata\Arguments\Headers\Handler\RawFaultDetail',
                'method' => 'deserializeFaultDetail',
            ],
        ];
    }

    public function deserializeFaultDetail(XmlDeserializationVisitor $visitor, \SimpleXMLElement $data, array $type, DeserializationContext $context): \SimpleXMLElement
    {
        return $data->children();
    }
}
