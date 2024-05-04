<?php

declare(strict_types=1);

namespace Project\RequestHandler\Dynamic;

use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\Dynamic\AbstractApiDynamicRequestHandler;
use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\Contract\JSONAPIRendererInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Service\HTMLRenderer;
use WebServCo\View\Service\JSONAPIRenderer;

/**
 * API request handler.
 */
final class ApiRequestHandler extends AbstractApiDynamicRequestHandler implements
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
            JSONAPIRendererInterface::class => JSONAPIRenderer::class,
            // Provide also a fallback HTML renderer to show a nice message if accessed via a browser
            HTMLRendererInterface::class => HTMLRenderer::class,
        ];
    }
}
