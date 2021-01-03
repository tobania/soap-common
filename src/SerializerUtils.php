<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata;

use JMS\Serializer\Serializer;
use Metadata\MetadataFactory;

class SerializerUtils
{
    /**
     * Get metadata factory from serializer, with any JMS Serializer version.
     */
    public static function getMetadataFactory(Serializer $serializer): MetadataFactory
    {
        $reflectionProperty = new \ReflectionProperty(Serializer::class, 'factory');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($serializer);
    }
}
