<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Metadata;

use GoetasWebservices\WsdlToPhp\Metadata\PhpMetadataGenerator;
use GoetasWebservices\XML\SOAPReader\SoapReader;
use GoetasWebservices\XML\WSDLReader\DefinitionsReader;

/**
 * This class is here to be used only while developing, should not be used on production.
 *
 * Class DevMetadataReader
 * @package GoetasWebservices\SoapServices\SoapCommon\Metadata
 */
class DevMetadataReader implements MetadataReaderInterface
{
    /**
     * @var array
     */
    private $metadataCache = [];
    /**
     * @var PhpMetadataGenerator
     */
    private $metadataGenerator;
    /**
     * @var DefinitionsReader
     */
    private $wsdlReader;
    /**
     * @var SoapReader
     */
    private $soapReader;

    public function __construct(PhpMetadataGenerator $metadataGenerator, SoapReader $soapReader, DefinitionsReader $wsdlReader)
    {
        $this->metadataGenerator = $metadataGenerator;
        $this->wsdlReader = $wsdlReader;
        $this->soapReader = $soapReader;
    }

    public function load($wsdl)
    {
        if (!isset($this->metadataCache[$wsdl])) {
            $this->wsdlReader->readFile($wsdl);
            $this->metadataCache[$wsdl] = $this->metadataGenerator->generate($this->soapReader->getServices());
        }

        return $this->metadataCache[$wsdl];
    }
}
