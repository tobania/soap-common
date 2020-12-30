<?php

declare(strict_types=1);

namespace GoetasWebservices\SoapServices\Metadata;

use GoetasWebservices\XML\WSDLReader\Exception\PortNotFoundException;
use GoetasWebservices\XML\WSDLReader\Exception\ServiceNotFoundException;

class MetadataUtils
{
    public static function getService(?string $serviceName, array $services): array
    {
        if ($serviceName && isset($services[$serviceName])) {
            return $services[$serviceName];
        } elseif ($serviceName) {
            throw new ServiceNotFoundException(sprintf('The service named %s can not be found', $serviceName));
        } else {
            return reset($services);
        }
    }

    public static function getPort(?string $portName, array $service): array
    {
        if ($portName && isset($service[$portName])) {
            return $service[$portName];
        } elseif ($portName) {
            throw new PortNotFoundException(sprintf('The port named %s can not be found', $portName));
        } else {
            return reset($service);
        }
    }
}
