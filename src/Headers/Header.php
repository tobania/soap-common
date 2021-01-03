<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Headers;

class Header
{
    /**
     * @var object
     */
    private $data;
    /**
     * @var array
     */
    private $options = [];

    public function __construct(object $data, array $options = [])
    {
        $this->data = $data;
        $this->options = $options;
    }

    public static function asMustUnderstand(object $data): Header
    {
        $header = new Header($data);
        $header->mustUnderstand();

        return $header;
    }

    public function getData(): object
    {
        return $this->data;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function mustUnderstand(): Header
    {
        $this->options['mustUnderstand'] = true;

        return $this;
    }
}
