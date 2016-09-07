<?php
namespace GoetasWebservices\SoapServices\SoapCommon\Metadata;

use Doctrine\Common\Inflector\Inflector;
use GoetasWebservices\XML\SOAPReader\Soap\Operation;
use GoetasWebservices\XML\SOAPReader\Soap\OperationMessage;
use GoetasWebservices\XML\SOAPReader\Soap\Service;
use GoetasWebservices\XML\SOAPReader\SoapReader;
use GoetasWebservices\XML\WSDLReader\DefinitionsReader;
use GoetasWebservices\XML\WSDLReader\Wsdl\PortType\Param;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PhpMetadataGenerator implements PhpMetadataGeneratorInterface
{
    protected $namespaces = [];

    private $baseNs = [
        'headers' => '\\SoapEnvelope\\Headers',
        'parts' => '\\SoapEnvelope\\Parts',
        'messages' => '\\SoapEnvelope\\Messages',
    ];

    public function __construct(array $namespaces = array(), array $baseNs = array())
    {
        foreach ($baseNs as $k => $ns) {
            if (isset($this->baseNs[$k])) {
                $this->baseNs[$k] = $ns;
            }
        }
        $this->namespaces = $namespaces;
    }

    public function addNamespace($ns, $phpNamespace)
    {
        $this->namespaces[$ns] = $phpNamespace;
        return $this;
    }

    /**
     * @param string $wsdl WSDL path
     * @return array
     */
    public function generateServices($wsdl)
    {
        $dispatcher = new EventDispatcher();
        $wsdlReader = new DefinitionsReader(null, $dispatcher);

        $soapReader = new SoapReader();
        $dispatcher->addSubscriber($soapReader);
        $wsdlReader->readFile($wsdl);

        $services = [];

        /**
         * @var $soapService Service
         */
        foreach ($soapReader->getServices() as $soapService) {
            $services[$soapService->getPort()->getService()->getName()][$soapService->getPort()->getName()] = [
                'operations' => $this->generateService($soapService),
                'endpoint' => $soapService->getAddress()
            ];
        }
        return $services;
    }

    protected function generateService(Service $service)
    {
        $operations = [];

        foreach ($service->getOperations() as $operation) {
            $operations[$operation->getOperation()->getName()] = $this->generateOperation($operation);
        }
        return $operations;
    }

    protected function generateOperation(Operation $soapOperation)
    {

        $operation = [
            'action' => $soapOperation->getAction(),
            'style' => $soapOperation->getStyle(),
            'name' => $soapOperation->getOperation()->getName(),
            'method' => Inflector::camelize($soapOperation->getOperation()->getName()),
            'input' => $this->generateInOut($soapOperation, $soapOperation->getInput(), $soapOperation->getOperation()->getPortTypeOperation()->getInput(), 'Input'),
            'output' => $this->generateInOut($soapOperation, $soapOperation->getOutput(), $soapOperation->getOperation()->getPortTypeOperation()->getOutput(), 'Output'),
            'fault' => []
        ];

        /**
         * @var $fault \GoetasWebservices\XML\SOAPReader\Soap\Fault
         */

        foreach ($soapOperation->getFaults() as $fault) {
            //$operation['fault'][$fault->getName()] = $fault->get;
            // @todo do faults metadata
        }

        return $operation;
    }

    protected function generateInOut(Operation $operation, OperationMessage $operationMessage, Param $param, $direction)
    {
        $xmlNs = $operation->getOperation()->getDefinition()->getTargetNamespace();
        if (!isset($this->namespaces[$xmlNs])) {
            throw new \Exception("Can not find a PHP namespace to be associated with '$xmlNs' XML namespace");
        }
        $ns = $this->namespaces[$xmlNs];
        $operation = [
            'message_fqcn' => $ns
                . $this->baseNs['messages'] . '\\'
                . Inflector::classify($operationMessage->getMessage()->getOperation()->getName())
                . $direction,
            'part_fqcn' => $ns
                . $this->baseNs['parts'] . '\\'
                . Inflector::classify($operationMessage->getMessage()->getOperation()->getName())
                . $direction,
            'parts' => array_keys($param->getMessage()->getParts())
        ];
        return $operation;
    }
}

