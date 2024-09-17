<?php

declare(strict_types=1);

namespace Project\Factory\Command;

use Override;
use Project\Command\Sandbox\TestCommand;
use WebServCo\Command\Contract\CommandFactoryInterface;
use WebServCo\Command\Contract\CommandRunnerInterface;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;

final class TestCommandFactory implements CommandFactoryInterface
{
    private const string LOGGER_CHANNEL = 'application';

    public function __construct(private ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    #[Override]
    public function createCommand(): CommandRunnerInterface
    {
        $serviceContainer = $this->applicationDependencyContainer->getServiceContainer();

        return new TestCommand(
            $serviceContainer->getLapTimer(),
            $serviceContainer->getOutputService(self::LOGGER_CHANNEL),
        );
    }
}
