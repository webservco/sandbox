<?php

declare(strict_types=1);

namespace Project\Instantiator\Controller;

use Project\Contract\Controller\APIControllerInterface;
use Project\Contract\Controller\ErrorControllerInterface;
use Project\Contract\Controller\SandboxControllerInterface;
use Project\Contract\Controller\StuffControllerInterface;
use WebServCo\Controller\Contract\SpecificModuleControllerInstantiatorInterface;
use WebServCo\Controller\Service\AbstractSpecificModuleControllerInstantiator;

final class SpecificModuleControllerInstantiator extends AbstractSpecificModuleControllerInstantiator implements
    SpecificModuleControllerInstantiatorInterface
{
    /**
     * @return array<string,string>
     */
    public function getAvailableModuleControllerInstantiators(): array
    {
        return [
            APIControllerInterface::class => APIModuleControllerInstantiator::class,
            ErrorControllerInterface::class => ErrorModuleControllerInstantiator::class,
            SandboxControllerInterface::class => SandboxModuleControllerInstantiator::class,
            StuffControllerInterface::class => StuffModuleControllerInstantiator::class,
        ];
    }
}
