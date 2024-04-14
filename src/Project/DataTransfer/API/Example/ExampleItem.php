<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Example;

use WebServCo\JSONAPI\Contract\Data\ItemInterface;

final readonly class ExampleItem implements ItemInterface
{
    public const TYPE = 'example_item';

    // @phpcs:ignore SlevomatCodingStandard.Classes.ForbiddenPublicProperty.ForbiddenPublicProperty
    public string $type;

    public function __construct(public int $id, public ExampleAttributes $attributes, public ExampleDataItemMeta $meta)
    {
        $this->type = self::TYPE;
    }
}
