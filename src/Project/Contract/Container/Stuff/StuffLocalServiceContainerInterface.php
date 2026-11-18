<?php

declare(strict_types=1);

namespace Project\Contract\Container\Stuff;

use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;
use WebServCo\Session\Contract\CsrfTokenServiceInterface;
use WebServCo\Session\Contract\FlashMessageServiceInterface;
use WebServCo\Stuff\Contract\Container\Storage\StuffStorageContainerInterface;

/**
 * Local service container with dependencies specific to current project or parts of the current project.
 */
interface StuffLocalServiceContainerInterface extends LocalDependencyContainerInterface
{
    /**
     * The CSRF token of the session (`webservco/session`): the hidden field of every form.
     */
    public function getCsrfTokenService(): CsrfTokenServiceInterface;

    /**
     * Flash messages (`webservco/session`): the result of an action, shown once on the next page.
     */
    public function getFlashMessageService(): FlashMessageServiceInterface;

    public function getFormFactoryContainer(): FormFactoryContainerInterface;

    public function getStorageContainer(): StuffStorageContainerInterface;
}
