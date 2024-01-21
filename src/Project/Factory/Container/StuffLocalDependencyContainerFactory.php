<?php

declare(strict_types=1);

namespace Project\Factory\Container;

use Project\Container\Stuff\LocalServiceContainer;
use WebServCo\Database\Factory\PDOContainerMySQLFactory;
use WebServCo\DependencyContainer\Contract\ApplicationDependencyContainerInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerFactoryInterface;
use WebServCo\DependencyContainer\Contract\LocalDependencyContainerInterface;

final class StuffLocalDependencyContainerFactory implements LocalDependencyContainerFactoryInterface
{
    public function __construct(private ApplicationDependencyContainerInterface $applicationDependencyContainer)
    {
    }

    public function createLocalDependencyContainer(): LocalDependencyContainerInterface
    {
        $pdoContainerFactory = new PDOContainerMySQLFactory(
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

        return new LocalServiceContainer(
            $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter(),
            $this->applicationDependencyContainer->getDataExtractionContainer(),
            $pdoContainerFactory->createPDOContainer(),
        );
    }
}
