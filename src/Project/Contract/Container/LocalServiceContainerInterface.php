<?php

declare(strict_types=1);

namespace Project\Contract\Container;

use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;

/**
 * Local service container with dependencies specific to current project or parts of the current project.
 */
interface LocalServiceContainerInterface extends LocalDependencyContainerInterface
{
}
