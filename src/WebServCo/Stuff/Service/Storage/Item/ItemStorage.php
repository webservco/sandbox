<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Service\Storage\Item;

use OutOfRangeException;
use WebServCo\Stuff\Contract\Storage\Item\ItemStorageInterface;
use WebServCo\Stuff\DataTransfer\Item\Item;
use WebServCo\Stuff\Service\Storage\AbstractStorage;

final class ItemStorage extends AbstractStorage implements ItemStorageInterface
{
    public function addItem(Item $item, ?int $parentItemId): int
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "INSERT INTO stuff_item (parent_item_id, item_name, item_description) VALUE (?, ?, ?)",
        );
        $stmt->execute([$parentItemId, $item->name, $item->description]);

        return (int) $this->pdoContainer->getPDOService()->getLastInsertId($this->pdoContainer->getPDO());
    }

    public function deleteItem(int $itemId): bool
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "DELETE FROM stuff_item WHERE item_id = ? LIMIT 1",
        );

        return $stmt->execute([$itemId]);
    }

    public function retrieveItem(int $itemId): Item
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "SELECT item_name, item_description
            FROM stuff_item WHERE item_id = ? LIMIT 1",
        );
        $stmt->execute([$itemId]);

        return $this->hydrateItem($this->pdoContainer->getPDOService()->fetchAssoc($stmt));
    }

    public function updateItem(Item $item, int $itemId): bool
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "UPDATE stuff_item SET item_name = ?, item_description = ? WHERE item_id = ? LIMIT 1",
        );

        return $stmt->execute([$item->name, $item->description, $itemId]);
    }

    /**
     * @param array<string,scalar|null> $data
     */
    private function hydrateItem(array $data): Item
    {
        if ($data === []) {
            throw new OutOfRangeException('Data is empty.');
        }

        return new Item(
            $this->dataExtractionContainer->getStrictArrayNonEmptyDataExtractionService()
            ->getNonEmptyString($data, 'item_name'),
            $this->dataExtractionContainer->getStrictArrayDataExtractionService()
            ->getString($data, 'item_description'),
        );
    }
}
