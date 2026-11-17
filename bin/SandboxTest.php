<?php

/**
 * Command runner.
 */

declare(strict_types=1);

namespace Project;

use Project\Factory\Command\TestCommandFactory;
use Throwable;
use WebServCo\Application\Factory\DefaultCommandApplicationFactory;
use WebServCo\Application\Service\DefaultApplicationInitializer;
use WebServCo\Error\Factory\DefaultErrorHandlingServiceFactory;

use function hrtime;

/**
 * Stopwatch.
 *
 * Initialize start time as early as possible.
 */
$startTime = (int) hrtime(true);

/**
 * Composer autoload.
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Initialization: lap timer, application dependency container, logger, configuration, exception handler.
 */
try {
    $applicationDependencyContainer = (new DefaultApplicationInitializer())->initialize(__DIR__ . '/..', $startTime);
} catch (Throwable) {
    /**
     * The initializer has logged the problem.
     */
    echo 'Initialization exception.';
    exit(1);
}

/**
 * Code above is common for any web controllers or command runners.
 * Code below is custom for current script.
 */

$commandFactory = new TestCommandFactory($applicationDependencyContainer);

/**
 * Application.
 *
 * Not necessary to be in the try block.
 * Exceptions are handled either in the application itself, or by the uncaught exception handler.
 */
$applicationFactory = new DefaultCommandApplicationFactory(
    new DefaultErrorHandlingServiceFactory(),
    $applicationDependencyContainer->getServiceContainer(),
);
$application = $applicationFactory->createDefaultCommandApplication($commandFactory->createCommand());
// @phpcs:enable
$application->bootstrap();
// No need to call `$application->shutdown();` it is registered as shutdown function.
exit($application->run() ? 0 : 1);
