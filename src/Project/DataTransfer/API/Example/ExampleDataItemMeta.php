<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Example;

use WebServCo\JSONAPI\Contract\Document\MetaInterface;

final readonly class ExampleDataItemMeta implements MetaInterface
{
    public function __construct(public string $key)
    {
    }
}
