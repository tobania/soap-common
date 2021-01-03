<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Headers;

class HeadersIncoming
{
    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var bool[]
     */
    private $understood = [];

    /**
     * @var object[]
     */
    private $mapping = [];

    public function addHeader(\SimpleXMLElement $header, ?object $mappedObject = null): void
    {
        $id = $this->findId($header);
        $this->headers[$id] = [$header, !empty($this->headers[$id][1])];
        $this->mapping[$id] = $this->mapping[$id] ?? ($mappedObject ? spl_object_id($mappedObject) : null);
    }

    public function addMustUnderstandHeader(\SimpleXMLElement $header, ?object $mappedObject = null): void
    {
        $id = $this->findId($header);
        $this->headers[$id] = [$header, true];
        $this->mapping[$id] = $this->mapping[$id] ?? ($mappedObject ? spl_object_id($mappedObject) : null);
    }

    /**
     * @return \SimpleXMLElement[]
     */
    public function headersNotUnderstood(): array
    {
        $headers = [];
        foreach ($this->headers as $id => $header) {
            if (!empty($header[1]) && empty($this->understood[$id])) {
                $headers[] = $header[0];
            }
        }

        return $headers;
    }

    /**
     * @param object|\SimpleXMLElement $header
     */
    public function isMustUnderstandHeader(object $header): bool
    {
        $id = $this->findId($header);

        return isset($this->headers[$id]) && !empty($this->headers[$id][1]);
    }

    /**
     * @param object|\SimpleXMLElement $header
     */
    public function understoodHeader(object $header): void
    {
        $id = $this->findId($header);

        $this->understood[$id] = true;
    }

    /**
     * @param object|\SimpleXMLElement $header
     */
    private function findId(object $header): string
    {
        if ($header instanceof \SimpleXMLElement) {
            return md5($header->asXML());
        }

        return array_search(spl_object_id($header), $this->mapping) ?: '';
    }

    /**
     * @return \SimpleXMLElement[]
     */
    public function getRawHeaders(): array
    {
        return array_column($this->headers, 0);
    }
}
