<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Metadata;

interface PhpMetadataGeneratorInterface
{
    /**
     * @param string $wsdl WSDL path
     * @return array
     */
    public function generateServices($wsdl);

    /**
     * @param string $ns
     * @param string $phpNamespace
     * @return void
     */
    public function addNamespace($ns, $phpNamespace);
}

