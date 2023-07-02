<?php

declare(strict_types=1);

namespace Project\Factory\View\Container\Sandbox;

use Project\View\Sandbox\TestView;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Factory\AbstractViewContainerFactory;

final class TestViewContainerFactory extends AbstractViewContainerFactory implements ViewContainerFactoryInterface
{
    /**
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $data
     * @phpcs:enable
     */
    public function createViewContainerFromData(array $data): ViewContainerInterface
    {
        return $this->createViewContainerFromView(
            new TestView(
                $this->processStringData('stringProperty', $data),
                $this->processIntData('intProperty', $data),
            ),
            'sandbox/test',
        );
    }
}
