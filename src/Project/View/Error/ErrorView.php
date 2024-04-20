<?php

declare(strict_types=1);

namespace Project\View\Error;

use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;

final class ErrorView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly string $code, public readonly string $message)
    {
    }
}
