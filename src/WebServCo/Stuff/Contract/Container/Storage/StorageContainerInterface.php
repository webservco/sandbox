<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Contract\Container\Storage;

interface StorageContainerInterface
{
    public function getItemStorageContainer(): ItemStorageContainerInterface;
}
