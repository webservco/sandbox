<?php

declare(strict_types=1);

namespace Project\View\Sandbox;

use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\Service\AbstractView;

final class TestView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly int $intProperty, public readonly string $stringProperty)
    {
    }
}
