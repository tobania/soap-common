<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments\Headers\Handler;

abstract class HeaderPlaceholder
{
    /**
     * @var object[]
     * @codingStandardsIgnoreLine
     */
    private $__headers = [];

    public function addHeader(object $header): void
    {
        // @codingStandardsIgnoreLine
        $this->__headers[] = $header;
    }
}
