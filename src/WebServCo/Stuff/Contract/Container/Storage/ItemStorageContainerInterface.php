<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Contract\Container\Storage;

use WebServCo\Stuff\Contract\Storage\Item\ItemEntityStorageInterface;
use WebServCo\Stuff\Contract\Storage\Item\ItemStorageInterface;
use WebServCo\Stuff\Contract\Storage\Item\SearchItemStorageInterface;

interface ItemStorageContainerInterface
{
    public function getItemEntityStorage(): ItemEntityStorageInterface;

    public function getItemStorage(): ItemStorageInterface;

    public function getSearchItemStorage(): SearchItemStorageInterface;
}
