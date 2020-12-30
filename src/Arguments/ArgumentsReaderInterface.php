<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata\Arguments;

interface ArgumentsReaderInterface
{
    /**
     * @param array $args
     * @param array $input
     */
    public function readArguments(array $args, array $input): object;
}
