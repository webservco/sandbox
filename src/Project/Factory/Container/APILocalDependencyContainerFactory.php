<?php

declare(strict_types=1);

namespace Project\Factory\Container;

use Override;
use Project\Container\API\LocalServiceContainer;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerFactoryInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;

final class APILocalDependencyContainerFactory implements LocalDependencyContainerFactoryInterface
{
    #[Override]
    public function createLocalDependencyContainer(): LocalDependencyContainerInterface
    {
        /**
         * Normally here we would initialize PDO etc.
         */

        return new LocalServiceContainer();
    }
}
