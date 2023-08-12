<?php

declare(strict_types=1);

namespace Project\Controller\Error;

use Fig\Http\Message\StatusCodeInterface;
use Project\Contract\Controller\ErrorControllerInterface;
use Project\View\Error\NotFoundView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\View\Contract\ViewContainerInterface;

final class NotFoundController extends AbstractErrorController implements ErrorControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        // Create view.
        $viewContainer = $this->createViewContainer($request);

        // Return response.
        return $this->createResponse($viewContainer, StatusCodeInterface::STATUS_NOT_FOUND);
    }

    private function createViewContainer(ServerRequestInterface $request): ViewContainerInterface
    {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new NotFoundView(
                $request->getMethod(),
                $request->getUri()->__toString(),
                $request->getHeaderLine('UserAgent'),
            ),
            'error/notfound',
        );
    }
}
