<?php

/**
 * Web accessible entry point (front controller).
 */

declare(strict_types=1);

namespace Project;

use Project\Factory\Application\ApplicationFactoryFactory;
use Psr\Log\NullLogger;
use Throwable;
use WebServCo\Configuration\Service\ConfigurationFileProcessor;
use WebServCo\Configuration\Service\IniServerConfigurationContainer;
use WebServCo\DependencyContainer\Service\ApplicationDependencyContainer;
use WebServCo\Exception\Factory\DefaultExceptionHandlerFactory;
use WebServCo\Stopwatch\Factory\LapTimerFactory;

use function hrtime;
use function realpath;
use function set_exception_handler;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
 * Stopwatch.
 *
 * Initialize start time as early as possible.
 */
$startTime = (int) hrtime(true);

/**
 * Configuration.
 */
$projectPath = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR;

/**
 * Composer autoload.
 *
 * @psalm-suppress UnresolvableInclude
 */
require_once sprintf('%svendor%sautoload.php', $projectPath, DIRECTORY_SEPARATOR);

/**
 * Logger (\Psr\Log\LoggerInterface).
 *
 * Create logger outside of the application so it can be used also in this section to log errors.
 * Create a null logger as first, to prevent logger existence checking in the catch block.
 */
$logger = new NullLogger();

/**
 * Initialization.
 */
try {
    /**
     * Stopwatch; Initialize timer with the start time from the beginning of the script run.
     */
    $lapTimerFactory = new LapTimerFactory();
    $lapTimer = $lapTimerFactory->createLapTimer();
    $lapTimer->start($startTime);
    $lapTimer->lap('autoload');

    /**
     * Setup application dependencies.
     */
    $applicationDependencyContainer = new ApplicationDependencyContainer($projectPath, $lapTimer);

    /**
     * Get a proper logger (\Psr\Log\LoggerInterface)
     * Any exception caught from now on will use this logger.
     */
    $logger = $applicationDependencyContainer->getServiceContainer()->getLogger('application');

    /**
     * Configuration.
     */
    $configurationContainer = new IniServerConfigurationContainer();
    $configurationFileProcessor = new ConfigurationFileProcessor(
        $configurationContainer->getConfigurationDataProcessor(),
        $configurationContainer->getConfigurationLoader(),
        $configurationContainer->getConfigurationSetter(),
    );
    $configurationFileProcessor->processConfigurationFile($projectPath, 'config', '.env.ini');

    /**
     * Exception handler.
     */
    $exceptionHandlerFactory = new DefaultExceptionHandlerFactory();
    set_exception_handler([$exceptionHandlerFactory->createUncaughtExceptionHandler($logger), 'handle']);

    $applicationDependencyContainer->getServiceContainer()->getLapTimer()->lap('initialization');
} catch (Throwable $e) {
    /**
     * Primitive handling of any error that happen during initialization.
     * Errors are actually logged here only if the logger was successfully created in the try block.
     */
    $logger->emergency($e->getMessage(), ['throwable' => $e]);
    echo 'Initialization exception.';
    exit(1);
}

/**
 * Code above is common for any web controllers or command runners.
 * Code below is custom for current script.
 */

/**
 * Application.
 *
 * Not necessary to be in the try block.
 * Exceptions are handled either in the application itself, or by the uncaught exception handler.
 */
$applicationFactoryFactory = new ApplicationFactoryFactory(
    $applicationDependencyContainer,
    $exceptionHandlerFactory->createExceptionHandler($logger),
);
$applicationFactory = $applicationFactoryFactory->createServerApplicationFactory($projectPath);
// @phpcs:disable SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
$application = $applicationFactory->createDefaultServerApplication(
    $applicationDependencyContainer->getServiceContainer()->getLapTimer(),
);
// @phpcs:enable
$application->bootstrap();
$application->run();
// No need to call `$application->shutdown();` it is registered as shutdown function.
