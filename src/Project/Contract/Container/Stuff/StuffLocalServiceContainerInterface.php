<?php

declare(strict_types=1);

namespace Project\Contract\Container\Stuff;

use Project\Contract\Container\LocalServiceContainerInterface;
use WebServCo\Stuff\Contract\Container\Storage\StorageContainerInterface;

/**
 * Local service container with dependencies specific to current project or parts of the current project.
 */
interface StuffLocalServiceContainerInterface extends LocalServiceContainerInterface
{
    public function getFormFactoryContainer(): FormFactoryContainerInterface;

    public function getStorageContainer(): StorageContainerInterface;
}
