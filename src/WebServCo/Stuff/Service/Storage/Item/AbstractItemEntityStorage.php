<?php

declare(strict_types=1);

namespace WebServCo\Stuff\Service\Storage\Item;

use OutOfRangeException;
use WebServCo\Stuff\DataTransfer\Item\Item;
use WebServCo\Stuff\Entity\Item\ItemEntity;
use WebServCo\Stuff\Service\Storage\AbstractStorage;

abstract class AbstractItemEntityStorage extends AbstractStorage
{
    /**
     * @param array<string,scalar|null> $data
     */
    protected function hydrateItemEntity(array $data): ItemEntity
    {
        if ($data === []) {
            throw new OutOfRangeException('Data is empty.');
        }

        return new ItemEntity(
            $this->dataExtractionContainer->getStrictArrayNonEmptyDataExtractionService()
                ->getNonEmptyInt($data, 'item_id'),
            new Item(
                $this->dataExtractionContainer->getStrictArrayNonEmptyDataExtractionService()
                    ->getNonEmptyString($data, 'item_name'),
                $this->dataExtractionContainer->getStrictArrayDataExtractionService()
                    ->getString($data, 'item_description'),
            ),
            $this->dataExtractionContainer->getStrictArrayNonEmptyDataExtractionService()
                ->getNonEmptyNullableInt($data, 'parent_item_id'),
        );
    }
}
