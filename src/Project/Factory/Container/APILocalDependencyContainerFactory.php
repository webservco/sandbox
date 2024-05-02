<?php

declare(strict_types=1);

namespace Project\Factory\Container;

use WebServCo\DependencyContainer\Contract\LocalDependencyContainerFactoryInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\JSONAPI\Container\APILocalServiceContainer;

final class APILocalDependencyContainerFactory extends AbstractLocalDependencyContainerFactory implements
    LocalDependencyContainerFactoryInterface
{
    public function createLocalDependencyContainer(): LocalDependencyContainerInterface
    {
        return new APILocalServiceContainer($this->applicationDependencyContainer->getDataExtractionContainer());
    }
}
