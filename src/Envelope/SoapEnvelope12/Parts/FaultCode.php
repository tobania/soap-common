<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Parts;

class FaultCode
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var FaultCode
     */
    private $subcode;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getSubcode(): ?FaultCode
    {
        return $this->subcode;
    }

    public function setSubcode(FaultCode $subcode): void
    {
        $this->subcode = $subcode;
    }

    public function __toString()
    {
        return $this->value . ($this->subcode ? ':' . $this->subcode : '');
    }
}
