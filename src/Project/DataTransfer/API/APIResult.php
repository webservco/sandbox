<?php

declare(strict_types=1);

namespace Project\DataTransfer\API;

use Project\Contract\DataTransfer\APIResultInterface;
use WebServCo\Data\Contract\Transfer\DataTransferInterface;

final class APIResult implements APIResultInterface, DataTransferInterface
{
    public function __construct(public readonly ?string $routePart3)
    {
    }
}
