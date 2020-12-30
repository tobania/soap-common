<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments\Headers;

class HeaderBag
{
    /**
     * @var object[]
     */
    private $headers = [];
    /**
     * @var object[]
     */
    private $mustUnderstandHeaders = [];

    public function __construct()
    {
    }

    public function hasHeader(object $header): bool
    {
        return isset($this->headers[spl_object_id($header)]);
    }

    public function addHeader(object $header): void
    {
        $this->headers[spl_object_id($header)] = $header;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function addMustUnderstandHeader(object $header): void
    {
        $this->mustUnderstandHeaders[spl_object_id($header)] = $header;
        $this->headers[spl_object_id($header)] = $header;
    }

    public function isMustUnderstandHeader(object $header): bool
    {
        return $this->hasHeader($header) && isset($this->mustUnderstandHeaders[spl_object_id($header)]);
    }

    public function removeMustUnderstandHeader(object $header): void
    {
        unset($this->mustUnderstandHeaders[spl_object_id($header)]);
    }

    /**
     * @return object[]
     */
    public function getMustUnderstandHeader(): array
    {
        return $this->mustUnderstandHeaders;
    }
}
