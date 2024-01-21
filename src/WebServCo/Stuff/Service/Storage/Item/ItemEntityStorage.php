<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Service\Storage\Item;

use Generator;
use WebServCo\Stuff\Contract\Storage\Item\ItemEntityStorageInterface;
use WebServCo\Stuff\Entity\Item\ItemEntity;

use function sprintf;

final class ItemEntityStorage extends AbstractItemEntityStorage implements ItemEntityStorageInterface
{
    private const ITEMS_LIMIT = 13;

    /**
     * Get a list of ItemEntity objects.
     *
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntity(?int $parentItemId): Generator
    {
        $params = [];
        $queryCondition = 'AND parent_item_id iS NULL';
        if ($parentItemId !== null) {
            $params[] = $parentItemId;
            $queryCondition = 'AND parent_item_id = ?';
        }

        $params[] = self::ITEMS_LIMIT;

        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            $this->getIterateQuery($queryCondition),
        );
        $stmt->execute($params);

        while ($row = $this->pdoContainer->getPDOService()->fetchAssoc($stmt)) {
            yield $this->hydrateItemEntity($row);
        }
    }

    /**
     * Iterate ItemEntity.
     *
     * Start from specific item and go up the tree iterating all parents.
     *
     * @phan-suppress PhanTypeMismatchArgumentNullable
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntityUpstream(ItemEntity $itemEntity): Generator
    {
        while ($itemEntity->parentItemId !== null) {
            /**
             * PHAN error:
             * PhanTypeMismatchArgumentNullable Argument 1 (locationId) is locationEntity->parentId of type ?int
             * but LocationEntityStorage::retrieveLocationEntity() takes int [...] (expected type to be non-nullable).
             * However, type is indeed not null (validated in the while loop)
             */
            $itemEntity = $this->retrieveItemEntity($itemEntity->parentItemId);

            yield $itemEntity;
        }
    }

    public function retrieveItemEntity(int $itemId): ItemEntity
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "SELECT item_id, parent_item_id, item_name, item_description
            FROM stuff_item WHERE item_id = ? LIMIT 1",
        );
        $stmt->execute([$itemId]);

        $row = $this->pdoContainer->getPDOService()->fetchAssoc($stmt);

        return $this->hydrateItemEntity($row);
    }

    private function getIterateQuery(string $queryCondition): string
    {
        return sprintf(
            "SELECT
                item_id,
                parent_item_id,
                item_name,
                item_description
            FROM stuff_item
            WHERE 1
            %s
            ORDER BY item_name ASC
            LIMIT 0, ?
            ",
            $queryCondition,
        );
    }
}
