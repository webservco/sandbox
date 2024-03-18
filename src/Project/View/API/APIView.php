<?php

declare(strict_types=1);

namespace Project\View\API;

use Project\Contract\DataTransfer\APIResultInterface;
use WebServCo\View\AbstractView;
use WebServCo\View\Contract\ViewInterface;

final class APIView extends AbstractView implements ViewInterface
{
    public function __construct(
        public readonly APIResultInterface $result,
        public readonly ?string $route,
        public readonly string $version,
    ) {
    }
}
