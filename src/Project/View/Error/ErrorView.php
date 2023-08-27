<?php

declare(strict_types=1);

namespace Project\View\Error;

use WebServCo\View\AbstractView;
use WebServCo\View\Contract\ViewInterface;

final class ErrorView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly string $code, public readonly string $message)
    {
    }
}
