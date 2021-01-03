<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope12\Messages;

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
}
