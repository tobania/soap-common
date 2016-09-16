<?php
namespace GoetasWebservices\SoapServices\SoapCommon\MetadataGenerator;

use GoetasWebservices\XML\SOAPReader\Soap\Service;

interface MetadataGeneratorInterface
{
    /**
     * @param Service[] $services
     * @return array
     */
    public function generate(array $services);
}

