<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope\Messages;

use GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope\Parts\Fault as FaultPart;
use GoetasWebservices\SoapServices\Metadata\Exception\Fault11Exception;
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

        if ($e instanceof ClientException) {
            $fault->setCode('SOAP:Client');
        } elseif ($e instanceof VersionMismatchException) {
            $fault->setCode('SOAP:VersionMismatch');
        } elseif ($e instanceof MustUnderstandException) {
            $fault->setCode('SOAP:MustUnderstand');
        } else {
            $fault->setCode('SOAP:Server');
        }

        if ($debug) {
            $fault->setString(implode("\n", array_merge([$e->getMessage()], explode("\n", (string) $e))));
        } else {
            $fault->setString($e->getMessage());
        }

        // @todo implement detail wrapping
        $fault->setDetail($e->getDetail());

        $faultBody->setFault($fault);

        return $faultEnvelope;
    }

    public function createException(ResponseInterface $response, RequestInterface $request, ?\Throwable $e = null): Fault11Exception
    {
        return new Fault11Exception($this->getBody()->getFault(), $response, $request, $e);
    }
}
