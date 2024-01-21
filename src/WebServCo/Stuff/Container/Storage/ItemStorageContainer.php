<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Container\Storage;

use WebServCo\Data\Contract\Extraction\DataExtractionContainerInterface;
use WebServCo\Database\Contract\PDOContainerInterface;
use WebServCo\Stuff\Contract\Container\Storage\ItemStorageContainerInterface;
use WebServCo\Stuff\Contract\Storage\Item\ItemEntityStorageInterface;
use WebServCo\Stuff\Contract\Storage\Item\ItemStorageInterface;
use WebServCo\Stuff\Contract\Storage\Item\SearchItemStorageInterface;
use WebServCo\Stuff\Service\Storage\Item\ItemEntityStorage;
use WebServCo\Stuff\Service\Storage\Item\ItemStorage;
use WebServCo\Stuff\Service\Storage\Item\SearchItemStorage;

final class ItemStorageContainer implements ItemStorageContainerInterface
{
    private ?ItemEntityStorageInterface $itemEntityStorage = null;
    private ?ItemStorageInterface $itemStorage = null;
    private ?SearchItemStorageInterface $searchItemStorage = null;

    public function __construct(
        private DataExtractionContainerInterface $dataExtractionContainer,
        private PDOContainerInterface $pdoContainer,
    ) {
    }

    public function getItemEntityStorage(): ItemEntityStorageInterface
    {
        if ($this->itemEntityStorage === null) {
            $this->itemEntityStorage = new ItemEntityStorage($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->itemEntityStorage;
    }

    public function getItemStorage(): ItemStorageInterface
    {
        if ($this->itemStorage === null) {
            $this->itemStorage = new ItemStorage($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->itemStorage;
    }

    public function getSearchItemStorage(): SearchItemStorageInterface
    {
        if ($this->searchItemStorage === null) {
            $this->searchItemStorage = new SearchItemStorage($this->dataExtractionContainer, $this->pdoContainer);
        }

        return $this->searchItemStorage;
    }
}
