<?php
namespace GoetasWebservices\SoapServices\SoapCommon\MetadataLoader;

interface MetadataLoaderInterface
{
    /**
     * @param $wsdl
     * @return array
     */
    public function load($wsdl);
}
