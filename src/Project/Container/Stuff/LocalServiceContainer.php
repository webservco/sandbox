<?php

declare(strict_types=1);

namespace Project\Container\Stuff;

use Project\Contract\Container\Stuff\FormFactoryContainerInterface;
use Project\Contract\Container\Stuff\StuffLocalServiceContainerInterface;
use WebServCo\Configuration\Contract\ConfigurationGetterInterface;
use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Database\Contract\PDOContainerInterface;
use WebServCo\Stuff\Container\Storage\StorageContainer;
use WebServCo\Stuff\Contract\Container\Storage\StorageContainerInterface;

final class LocalServiceContainer implements StuffLocalServiceContainerInterface
{
    private ?FormFactoryContainerInterface $formFactoryContainer = null;
    private ?StorageContainerInterface $storageContainer = null;

    public function __construct(
        private ConfigurationGetterInterface $configurationGetter,
        private DataExtractionContainerInterface $dataExtractionContainer,
        private PDOContainerInterface $pdoContainer,
    ) {
    }

    public function getFormFactoryContainer(): FormFactoryContainerInterface
    {
        if ($this->formFactoryContainer === null) {
            $this->formFactoryContainer = new FormFactoryContainer($this->configurationGetter);
        }

        return $this->formFactoryContainer;
    }

    public function getStorageContainer(): StorageContainerInterface
    {
        if ($this->storageContainer === null) {
            $this->storageContainer = new StorageContainer($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->storageContainer;
    }
}
