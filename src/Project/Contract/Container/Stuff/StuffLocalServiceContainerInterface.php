<?php

declare(strict_types=1);

namespace Project\Contract\Container\Stuff;

use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Stuff\Contract\Container\Storage\StuffStorageContainerInterface;

/**
 * Local service container with dependencies specific to current project or parts of the current project.
 */
interface StuffLocalServiceContainerInterface extends LocalDependencyContainerInterface
{
    public function getFormFactoryContainer(): FormFactoryContainerInterface;

    public function getStorageContainer(): StuffStorageContainerInterface;
}
