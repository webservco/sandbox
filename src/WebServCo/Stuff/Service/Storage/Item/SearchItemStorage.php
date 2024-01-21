<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Service\Storage\Item;

use Generator;
use WebServCo\Stuff\Contract\Storage\Item\SearchItemStorageInterface;

use function sprintf;

final class SearchItemStorage extends AbstractItemEntityStorage implements SearchItemStorageInterface
{
    private const ITEMS_LIMIT = 13;

    /**
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntity(string $searchString): Generator
    {
        $stmt = $this->pdoContainer->getPDOService()->prepareStatement(
            $this->pdoContainer->getPDO(),
            "SELECT
                item_id,
                parent_item_id,
                item_name,
                item_description
            FROM stuff_item
            WHERE item_name LIKE ? OR item_description LIKE ?
            ORDER BY item_name ASC
            LIMIT 0, ?",
        );
        $stmt->execute([sprintf('%%%s%%', $searchString), sprintf('%%%s%%', $searchString), self::ITEMS_LIMIT]);

        while ($row = $this->pdoContainer->getPDOService()->fetchAssoc($stmt)) {
            yield $this->hydrateItemEntity($row);
        }
    }
}
