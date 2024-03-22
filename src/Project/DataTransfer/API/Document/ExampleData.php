<?php

declare(strict_types=1);

namespace Project\DataTransfer\API\Document;

use WebServCo\JSONAPI\Contract\Document\DataInterface;

final readonly class ExampleData implements DataInterface
{
    public function __construct(public ?string $routePart3, public string $userId)
    {
    }
}
