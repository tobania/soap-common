<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Headers;

class HeadersOutgoing
{
    /**
     * @var array
     */
    private $headers = [];

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function addHeader(Header $header): void
    {
        $this->headers[] = $header;
    }

    /**
     * @return Header[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
