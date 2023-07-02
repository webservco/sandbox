<?php

declare(strict_types=1);

namespace Project\RequestHandler\ThreePart;

use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\ThreePart\AbstractThreePartRequestHandler;
use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\Contract\JSONRendererInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Service\HTMLRenderer;
use WebServCo\View\Service\JSONRenderer;

/**
 * API request handler.
 */
final class ApiRequestHandler extends AbstractThreePartRequestHandler implements
    RequestHandlerInterface,
    ViewRendererListInterface
{
    /**
     * @return array<string,string> interface/implementation
     */
    public function getAvailableViewRenderers(): array
    {
        // @phpcs:ignore SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys.IncorrectKeyOrder
        return [
            JSONRendererInterface::class => JSONRenderer::class,
            // Provide also a fallback HTML renderer to show a nice message if accessed via a browser
            HTMLRendererInterface::class => HTMLRenderer::class,
        ];
    }
}
