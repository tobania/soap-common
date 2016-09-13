<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Metadata;

class PhpMetadataReader implements MetadataReaderInterface
{
    /**
     * @var array
     */
    private $metadataMap = [];
    /**
     * @var array
     */
    private $metadataCache = [];

    public function __construct(array $metadataMap)
    {
        $this->metadataMap = $metadataMap;
    }

    public function load($wsdl)
    {
        if (!isset($this->metadataCache[$wsdl])) {
            if (!isset($this->metadataMap[$wsdl])) {
                throw new \Exception(sprintf("Can not find metadata information for %s", $wsdl));
            }
            $this->metadataCache[$wsdl] = require $this->metadataMap[$wsdl];
        }

        return $this->metadataCache[$wsdl];
    }
}
