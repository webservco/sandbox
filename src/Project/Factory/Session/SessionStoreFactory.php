<?php

declare(strict_types=1);

namespace Project\Factory\Session;

use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\Session\Contract\SessionStoreInterface;
use WebServCo\Session\Service\SessionStore;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Creates the session store (`webservco/session`): the session service of the application container (`COOKIE_*`,
 * `SESSION_*` configuration), with its files in `var/sessions`.
 */
final class SessionStoreFactory
{
    public const string STORAGE_DIRECTORY = 'var/sessions';

    public function __construct(private ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    public function createSessionStore(): SessionStoreInterface
    {
        return new SessionStore(
            $this->applicationDependencyContainer->getDataExtractionContainer()
                ->getStrictArrayNonEmptyDataExtractionService(),
            $this->applicationDependencyContainer->getServiceContainer()->getSessionService(),
            $this->createStoragePath(),
        );
    }

    /**
     * The directory of the session files. Code that starts the session itself (`AuthenticationMiddleware`) starts
     * it there too: a session started elsewhere can't be resumed.
     */
    public function createStoragePath(): string
    {
        return sprintf(
            '%s%s%s',
            rtrim(
                $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                    ->getString('PROJECT_PATH'),
                DIRECTORY_SEPARATOR,
            ),
            DIRECTORY_SEPARATOR,
            self::STORAGE_DIRECTORY,
        );
    }
}
