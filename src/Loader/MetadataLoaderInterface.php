<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Loader;

interface MetadataLoaderInterface
{
    public function load(string $wsdl): array;
}
