<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata;

use Metadata\MetadataFactory;

class SerializerUtils
{
    /**
     * Get metadata factory from serializer, with any JMS Serializer version.
     */
    public static function getMetadataFactory(object $serializer): MetadataFactory
    {
        // JMS Serializer 2.x & 3.x
        $reflectionProperty = new \ReflectionProperty(get_class($serializer), 'factory');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($serializer);
    }
}
