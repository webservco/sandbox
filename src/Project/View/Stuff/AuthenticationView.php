<?php

declare(strict_types=1);

namespace Project\View\Stuff;

use WebServCo\Form\Contract\FormInterface;
use WebServCo\View\Contract\ViewInterface;
use WebServCo\View\View\AbstractView;
use WebServCo\View\View\CommonView;

/**
 * Individual location.
 */
final class AuthenticationView extends AbstractView implements ViewInterface
{
    public function __construct(public readonly CommonView $commonView, public readonly FormInterface $form)
    {
    }
}
