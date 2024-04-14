<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Example;

final readonly class ExampleAttributes
{
    public function __construct(public ?string $routePart3, public string $userId)
    {
    }
}
