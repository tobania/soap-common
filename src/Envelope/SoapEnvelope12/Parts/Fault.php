<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Parts;

use GoetasWebservices\SoapServices\Metadata\Envelope\Fault as BaseFault;

/**
 * Class representing DoSomethingInput
 */
class Fault extends BaseFault
{
    /**
     * @var FaultCode
     */
    private $code;
    /**
     * @var string[]
     */
    private $reason = [];
    /**
     * @var object
     */
    private $detail;
    /**
     * @var string
     */
    private $role;
    /**
     * @var string
     */
    private $node;

    public function getDetail(): ?object
    {
        return $this->detail;
    }

    public function setDetail(?object $detail): void
    {
        $this->detail = $detail;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getNode(): ?string
    {
        return $this->node;
    }

    public function setNode(string $node): void
    {
        $this->node = $node;
    }

    public function getCode(): ?FaultCode
    {
        return $this->code;
    }

    public function setCode(FaultCode $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string[]
     */
    public function getReason(): array
    {
        return $this->reason;
    }

    /**
     * @param string[] $reason
     */
    public function setReason(array $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @var \SimpleXMLElement
     */
    private $rawDetail;

    public function getRawDetail(): ?\SimpleXMLElement
    {
        return $this->rawDetail;
    }

    public function setRawDetail(\SimpleXMLElement $rawDetail): void
    {
        $this->rawDetail = $rawDetail;
    }
}
