<?php

declare(strict_types=1);

namespace Project\View\API;

use Project\DataTransfer\API\APIResult;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\Service\AbstractView;

final class APIView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly APIResult $result, public readonly ?string $route)
    {
    }
}
