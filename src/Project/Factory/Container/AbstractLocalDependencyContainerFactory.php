<?php

declare(strict_types=1);

namespace Project\Factory\Container;

use WebServCo\Database\Factory\PDOContainerMySQLFactory;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerFactoryInterface;

abstract class AbstractLocalDependencyContainerFactory implements LocalDependencyContainerFactoryInterface
{
    public function __construct(protected ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    protected function createPdoContainerFactory(): PDOContainerMySQLFactory
    {
        return new PDOContainerMySQLFactory(
            $this->applicationDependencyContainer->getServiceContainer()
                ->getConfigurationGetter()->getString('DB_HOST'),
            (int) $this->applicationDependencyContainer->getServiceContainer()
                ->getConfigurationGetter()->getString('DB_PORT'),
            $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                ->getString('DB_NAME'),
            $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                ->getString('DB_USER'),
            $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                ->getString('DB_PASSWORD'),
        );
    }
}
