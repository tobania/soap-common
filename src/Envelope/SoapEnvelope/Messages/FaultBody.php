<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope\Messages;

use GoetasWebservices\SoapServices\Metadata\Envelope\SoapEnvelope\Parts\Fault;

/**
 * Class representing Fault
 */
class FaultBody
{
    /**
     * @var Fault $fault
     */
    private $fault = null;

    /**
     * Gets as fault
     */
    public function getFault(): Fault
    {
        return $this->fault;
    }

    /**
     * Sets a new fault
     */
    public function setFault(Fault $fault): self
    {
        $this->fault = $fault;

        return $this;
    }
}
