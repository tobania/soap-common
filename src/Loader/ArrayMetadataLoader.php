<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Loader;

use GoetasWebservices\SoapServices\Metadata\Exception\MetadataException;

class ArrayMetadataLoader implements MetadataLoaderInterface
{
    /**
     * @var array
     */
    private $metadata = [];

    public function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function addMetadata(string $wsdl, array $metadata): void
    {
        $this->metadata[$wsdl] = $metadata;
    }

    public function load(string $wsdl): array
    {
        if (!isset($this->metadata[$wsdl])) {
            throw new MetadataException(sprintf('Can not load metadata information for %s', $wsdl));
        }

        return $this->metadata[$wsdl];
    }
}
