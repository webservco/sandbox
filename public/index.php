<?php

/**
 * Web accessible entry point (front controller).
 */

declare(strict_types=1);

namespace Project;

use Project\Factory\Application\ApplicationFactoryFactory;
use Throwable;
use WebServCo\Application\Service\DefaultApplicationInitializer;

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

/*
 * Code above is common for any web controllers or command runners.
 * Code below is custom for current script.
 */

/**
 * Application.
 *
 * Not necessary to be in the try block.
 * Exceptions are handled either in the application itself, or by the uncaught exception handler.
 */
$applicationFactoryFactory = new ApplicationFactoryFactory($applicationDependencyContainer);
$applicationFactory = $applicationFactoryFactory->createServerApplicationFactory();
$application = $applicationFactory->createDefaultServerApplication();
$application->bootstrap();
$application->run();
// No need to call `$application->shutdown();` it is registered as shutdown function.
