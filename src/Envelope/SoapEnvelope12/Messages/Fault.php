<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Messages;

use GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Parts\Fault as FaultPart;
use GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Parts\FaultCode;
use GoetasWebservices\SoapServices\Metadata\Exception\Fault12Exception;
use GoetasWebservices\SoapServices\Metadata\Exception\FaultException as FaultExceptionMeta;
use GoetasWebservices\SoapServices\SoapServer\Exception\ClientException;
use GoetasWebservices\SoapServices\SoapServer\Exception\FaultException;
use GoetasWebservices\SoapServices\SoapServer\Exception\MustUnderstandException;
use GoetasWebservices\SoapServices\SoapServer\Exception\ServerException;
use GoetasWebservices\SoapServices\SoapServer\Exception\VersionMismatchException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class representing Body
 */
class Fault
{
    /**
     * @var FaultBody $body
     */
    private $body = null;

    /**
     * Gets as body
     */
    public function getBody(): FaultBody
    {
        return $this->body;
    }

    /**
     * Sets a new body
     */
    public function setBody(FaultBody $body): self
    {
        $this->body = $body;

        return $this;
    }

    public static function fromException(\Throwable $e, bool $debug = false): self
    {
        $faultEnvelope = new self();
        $faultBody = new FaultBody();
        $faultEnvelope->setBody($faultBody);

        $fault = new FaultPart();
        if (!$e instanceof FaultException) {
            $e = new ServerException($e->getMessage(), $e->getCode(), $e);
        }

        $faultCode = new FaultCode();

        if ($e instanceof VersionMismatchException) {
            $faultCode->setValue('SOAP:VersionMismatch');
        } elseif ($e instanceof MustUnderstandException) {
            $faultCode->setValue('SOAP:MustUnderstand');
        } elseif ($e instanceof ClientException) {
            $faultCode->setValue('SOAP:Sender');
        } else {
            $faultCode->setValue('SOAP:Receiver');
        }

        if (0 !== $e->getCode()) {
            $subFaultCode = new FaultCode();
            $subFaultCode->setValue((string) $e->getCode());

            $faultCode->setSubcode($subFaultCode);
        }

        $fault->setCode($faultCode);
        if ($debug) {
            $fault->setReason(array_merge([$e->getMessage()], explode("\n", (string) $e)));
        } else {
            $fault->setReason(explode("\n", $e->getMessage()));
        }

        // @todo implement detail wrapping
        $fault->setDetail($e->getDetail());

        $faultBody->setFault($fault);

        return $faultEnvelope;
    }

    /**
     * @param \Exception $e
     */
    public function createException(ResponseInterface $response, RequestInterface $request, ?\Throwable $e = null): FaultExceptionMeta
    {
        return new Fault12Exception($this->getBody()->getFault(), $response, $request, $e);
    }
}
