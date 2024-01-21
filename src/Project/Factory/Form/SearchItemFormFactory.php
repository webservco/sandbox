<?php

declare(strict_types=1);

namespace Project\Factory\Form;

use Project\Contract\Factory\Stuff\SearchItemFormFactoryInterface;
use WebServCo\Form\Contract\FormFieldInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Form\Service\Form;
use WebServCo\Form\Service\FormField;

final class SearchItemFormFactory extends AbstractFormFactory implements SearchItemFormFactoryInterface
{
    public function createForm(): FormInterface
    {
        return new Form(
            [
                0 => $this->createSearchField(),
            ],
            $this->getGeneralFilters(),
            $this->getGeneralValidators(),
        );
    }

    private function createSearchField(): FormFieldInterface
    {
        return new FormField(
            [
            ],
            'search_item',
            true,
            'search_item',
            'Search item',
            'Search',
            [
                $this->getMinimumLengthValidator(2),
                $this->getMaximumLengthValidator(90),
            ],
            null,
        );
    }
}
