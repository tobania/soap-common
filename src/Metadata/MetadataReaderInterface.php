<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Metadata;

interface MetadataReaderInterface
{
    /**
     * @param $wsdl
     * @return array
     */
    public function load($wsdl);
}
