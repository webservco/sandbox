<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Contract\Storage\Item;

use WebServCo\Stuff\DataTransfer\Item\Item;

interface ItemStorageInterface
{
    public function addItem(Item $item, ?int $parentItemId): int;

    public function deleteItem(int $itemId): bool;

    public function retrieveItem(int $itemId): Item;

    public function updateItem(Item $item, int $itemId): bool;
}
