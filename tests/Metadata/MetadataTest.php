<?php

namespace GoetasWebservices\SoapServices\SoapCommon\Tests;

use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\ArrayCache;
use GoetasWebservices\SoapServices\SoapCommon\Metadata\CachedPhpMetadataGenerator;
use GoetasWebservices\SoapServices\SoapCommon\Metadata\PhpMetadataGenerator;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    protected $metadataGenerator;
    public function setUp()
    {
        $this->metadataGenerator = new PhpMetadataGenerator(['http://www.example.org/test/' => '']);
    }
    public function testBuildServer()
    {

        $services = $this->metadataGenerator->generateServices(__DIR__ . '/../Fixtures/test.wsdl');
        $this->assertExpectedServices($services);
    }

    public function assertExpectedServices(array $services)
    {
        $expected = require __DIR__ . '/../Fixtures/test.php';
        return $this->assertEquals($expected, $services);
    }

    public function testCachedMetadata()
    {
        $metadataGenerator = new CachedPhpMetadataGenerator($this->metadataGenerator, new DoctrineCachePool(new ArrayCache()));
        $services = $metadataGenerator->generateServices(__DIR__ . '/../Fixtures/test.wsdl');
        $this->assertExpectedServices($services);

        $metadataGenerator->generateServices(__DIR__ . '/../Fixtures/test.wsdl');
    }
}
