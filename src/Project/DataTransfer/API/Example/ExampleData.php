<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Example;

use WebServCo\JSONAPI\Contract\Document\DataInterface;

final readonly class ExampleData implements DataInterface
{
    public const TYPE = 'example_data';

    // @phpcs:ignore SlevomatCodingStandard.Classes.ForbiddenPublicProperty.ForbiddenPublicProperty
    public string $type;

    public function __construct(public int $id, public ExampleAttributes $attributes, public ExampleDataItemMeta $meta)
    {
        $this->type = self::TYPE;
    }
}
