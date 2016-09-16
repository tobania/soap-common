<?php

namespace GoetasWebservices\SoapServices\SoapCommon\Tests\Metadata;

use GoetasWebservices\SoapServices\SoapCommon\MetadataGenerator\MetadataGenerator;
use GoetasWebservices\XML\SOAPReader\SoapReader;
use GoetasWebservices\XML\WSDLReader\DefinitionsReader;
use GoetasWebservices\Xsd\XsdToPhp\Naming\ShortNamingStrategy;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    protected $metadataGenerator;

    public function setUp()
    {
        $naming = new ShortNamingStrategy();
        $this->metadataGenerator = new MetadataGenerator($naming, ['http://www.example.org/test/' => 'TestNs']);
    }

    public function testMetadata()
    {

        $dispatcher = new EventDispatcher();
        $wsdlReader = new DefinitionsReader(null, $dispatcher);
        $soapReader = new SoapReader();
        $dispatcher->addSubscriber($soapReader);
        $wsdlReader->readFile(__DIR__ . '/../Fixtures/test.wsdl');

        $services = $this->metadataGenerator->generate($soapReader->getServices());
        $this->assertExpectedServices($services);
    }

    public function assertExpectedServices(array $services)
    {
        $expected = require __DIR__ . '/../Fixtures/test.php';
        return $this->assertEquals($expected, $services);
    }
}
