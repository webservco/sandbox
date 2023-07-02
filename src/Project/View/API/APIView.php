<?php

declare(strict_types=1);

namespace Project\View\API;

use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\Service\AbstractView;

final class APIView extends AbstractView implements ViewInterface
{
    /**
     * @param array<string,int|string|null> $result
     */
    public function __construct(public readonly string $route, public readonly array $result)
    {
    }
}
