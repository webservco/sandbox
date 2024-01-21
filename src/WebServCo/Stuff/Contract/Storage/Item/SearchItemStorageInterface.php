<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Contract\Storage\Item;

use Generator;

interface SearchItemStorageInterface
{
    /**
     * @return \Generator<\WebServCo\Stuff\Entity\Item\ItemEntity>
     */
    public function iterateItemEntity(string $searchString): Generator;
}
