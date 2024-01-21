<?php

declare(strict_types=1);

namespace Project\RequestHandler\ThreePart;

use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebServCo\Http\Service\Message\Request\RequestHandler\ThreePart\AbstractThreePartRequestHandler;
use WebServCo\View\Contract\HTMLRendererInterface;
use WebServCo\View\Contract\JSONRendererInterface;
use WebServCo\View\Contract\ViewRendererListInterface;
use WebServCo\View\Service\HTMLRenderer;
use WebServCo\View\Service\JSONRenderer;

/**
 * Request handler, using the three part routing system.
 */
final class StuffRequestHandler extends AbstractThreePartRequestHandler implements
    RequestHandlerInterface,
    ViewRendererListInterface
{
    /**
     * @return array<string,string> interface/implementation
     */
    public function getAvailableViewRenderers(): array
    {
        return [
            HTMLRendererInterface::class => HTMLRenderer::class,
            JSONRendererInterface::class => JSONRenderer::class,
        ];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->getRoutePart2($request);

        return match ($route) {
            'qwerty' => $this->handleQwerty(),
            default => $this->handleDefault($request),
        };
    }

    /**
     * Handle request (default).
     *
     * Default functionality: let parent class handle the request.
     * This is the main functionality, located in the parent class.
     */
    private function handleDefault(ServerRequestInterface $request): ResponseInterface
    {
        return parent::handle($request);
    }

    /**
     * Handle request (qwerty).
     *
     * Special situation, just to explore what it's possible to do.
     */
    private function handleQwerty(): ResponseInterface
    {
        throw new DomainException('Not implemented.');
    }
}
