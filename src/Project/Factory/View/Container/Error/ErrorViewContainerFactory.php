<?php

declare(strict_types=1);

namespace Project\Factory\View\Container\Error;

use Project\View\Error\ErrorView;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Factory\AbstractViewContainerFactory;

final class ErrorViewContainerFactory extends AbstractViewContainerFactory implements ViewContainerFactoryInterface
{
    /**
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $data
     * @phpcs:enable
     */
    public function createViewContainerFromData(array $data): ViewContainerInterface
    {
        return $this->createViewContainerFromView(
            new ErrorView(
                $this->processStringData('code', $data),
                $this->processStringData('message', $data),
            ),
            'error/error',
        );
    }
}
