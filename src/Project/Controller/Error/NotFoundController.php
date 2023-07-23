<?php

declare(strict_types=1);

namespace Project\Controller\Error;

use Fig\Http\Message\StatusCodeInterface;
use Project\Contract\Controller\ErrorControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class NotFoundController extends AbstractErrorController implements ErrorControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Data processing would go here (use services).

        // Gathered after data processing.
        $data = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->__toString(),
            'userAgent' => $request->getHeaderLine('UserAgent'),
        ];

        // Log.
        $this->getLogger(self::class)->debug('Data debug (see context).', $data);

        // Create view.
        $viewContainer = $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromData($data);

        // Return response.
        return $this->createResponse($viewContainer, StatusCodeInterface::STATUS_NOT_FOUND);
    }
}
