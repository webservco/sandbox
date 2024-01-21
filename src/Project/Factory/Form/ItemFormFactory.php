<?php

declare(strict_types=1);

namespace Project\Factory\Form;

use Project\Contract\Factory\Stuff\ItemFormFactoryInterface;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Form\Service\Form;
use WebServCo\Form\Service\FormField;
use WebServCo\Stuff\DataTransfer\Item\Item;

final class ItemFormFactory extends AbstractFormFactory implements ItemFormFactoryInterface
{
    private ?Item $item = null;

    public function createForm(): FormInterface
    {
        return new Form(
            [
                0 => $this->createDescriptionField($this->item),
                1 => $this->createNameField($this->item),
            ],
            $this->getGeneralFilters(),
            $this->getGeneralValidators(),
        );
    }

    public function setItem(Item $item): bool
    {
        $this->item = $item;

        return true;
    }

    private function createDescriptionField(?Item $item): FormFieldInterface
    {
        return new FormField(
            [
            ],
            'description',
            false,
            'description',
            'Item description',
            'Description',
            [
                $this->getMinimumLengthValidator(3),
                $this->getMaximumLengthValidator(255),

            ],
            $item !== null
                ? $item->description
                : null,
        );
    }

    private function createNameField(?Item $item): FormFieldInterface
    {
        return new FormField(
            [
            ],
            'name',
            true,
            'name',
            'Item name',
            'Name',
            [
                $this->getMinimumLengthValidator(3),
                $this->getMaximumLengthValidator(90),
            ],
            $item !== null
                ? $item->name
                : null,
        );
    }
}
