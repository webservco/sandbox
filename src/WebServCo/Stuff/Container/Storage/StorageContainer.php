<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Container\Storage;

use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Database\Contract\PDOContainerInterface;
use WebServCo\Stuff\Contract\Container\Storage\ItemStorageContainerInterface;
use WebServCo\Stuff\Contract\Container\Storage\StorageContainerInterface;

final class StorageContainer implements StorageContainerInterface
{
    private ?ItemStorageContainerInterface $itemStorageContainer = null;

    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private PDOContainerInterface $pdoContainer,
    ) {
    }

    public function getItemStorageContainer(): ItemStorageContainerInterface
    {
        if ($this->itemStorageContainer === null) {
            $this->itemStorageContainer = new ItemStorageContainer($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->itemStorageContainer;
    }
}
