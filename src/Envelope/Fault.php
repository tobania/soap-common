<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Envelope;

abstract class Fault
{
    abstract public function getRawDetail(): ?\SimpleXMLElement;

    abstract public function setRawDetail(\SimpleXMLElement $rawDetail): void;
}
