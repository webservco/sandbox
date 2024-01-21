<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Contract\Storage\Item;

use Generator;
use WebServCo\Stuff\Entity\Item\ItemEntity;

interface ItemEntityStorageInterface
{
    /**
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntity(?int $parentItemId): Generator;

    /**
     * Iterate ItemEntity.
     *
     * Start from specific item and go up the tree iterating all parents.
     *
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntityUpstream(ItemEntity $itemEntity): Generator;

    public function retrieveItemEntity(int $itemId): ItemEntity;
}
