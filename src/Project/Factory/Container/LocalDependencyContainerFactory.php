<?php

declare(strict_types=1);

namespace Project\Factory\Container;

use Project\Container\LocalServiceContainer;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerFactoryInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;

final class LocalDependencyContainerFactory implements LocalDependencyContainerFactoryInterface
{
    public function createLocalDependencyContainer(): LocalDependencyContainerInterface
    {
        return new LocalServiceContainer();
    }
}
