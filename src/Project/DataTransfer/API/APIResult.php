<?php

declare(strict_types=1);

namespace Project\DataTransfer\API;

use WebServCo\Data\Contract\Transfer\DataTransferInterface;

final class APIResult implements DataTransferInterface
{
    public function __construct(public readonly ?string $routePart3, public readonly int $version)
    {
    }
}
