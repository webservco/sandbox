<?php

declare(strict_types=1);

namespace Project\View\Sandbox;

use WebServCo\View\AbstractView;
use WebServCo\View\Contract\ViewInterface;

final class TestView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly int $intProperty, public readonly string $stringProperty)
    {
    }
}
