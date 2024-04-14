<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Example;

use WebServCo\JSONAPI\Contract\Document\MetaInterface;

final readonly class ExampleDocumentMeta implements MetaInterface
{
    public function __construct(public ?string $route, public string $version)
    {
    }
}
