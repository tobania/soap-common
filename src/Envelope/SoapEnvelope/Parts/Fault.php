<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope\Parts;

use GoetasWebservices\SoapServices\Metadata\Envelope\Fault as BaseFault;

class Fault extends BaseFault
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $actor;
    /**
     * @var string
     */
    private $string;

    /**
     * @var string
     */
    private $detail;

    public function getActor(): ?string
    {
        return $this->actor;
    }

    public function setActor(string $actor): void
    {
        $this->actor = $actor;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getString(): ?string
    {
        return $this->string;
    }

    public function setString(string $string): void
    {
        $this->string = $string;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): void
    {
        $this->detail = $detail;
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
