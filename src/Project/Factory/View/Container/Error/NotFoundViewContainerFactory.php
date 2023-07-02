<?php

declare(strict_types=1);

namespace Project\Factory\View\Container\Error;

use Project\View\Error\NotFoundView;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Factory\AbstractViewContainerFactory;

final class NotFoundViewContainerFactory extends AbstractViewContainerFactory implements ViewContainerFactoryInterface
{
    /**
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $data
     * @phpcs:enable
     */
    public function createViewContainerFromData(array $data): ViewContainerInterface
    {
        return $this->createViewContainerFromView(
            new NotFoundView(
                $this->processStringData('method', $data),
                $this->processStringData('uri', $data),
                $this->processStringData('userAgent', $data),
            ),
            'error/notfound',
        );
    }
}
