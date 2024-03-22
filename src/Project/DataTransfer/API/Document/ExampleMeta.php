<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Document;

use WebServCo\JSONAPI\Contract\Document\MetaInterface;

final readonly class ExampleMeta implements MetaInterface
{
    public function __construct(public ?string $route, public string $version)
    {
    }
}
