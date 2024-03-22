<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Contract\Controller\APIControllerInterface;
use Project\DataTransfer\API\Document\ExampleData;
use Project\DataTransfer\API\Document\ExampleMeta;
use Project\View\API\ItemView;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\JSONAPI\DataTransfer\Document\Jsonapi;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * A general API Controller.
 */
final class APIController extends AbstractAPIController implements APIControllerInterface
{
    protected function createViewContainer(ServerRequestInterface $request, string $userId): ViewContainerInterface
    {
        /**
         * Do not simply assume a JSONRendererInterface will be used / enforced.
         * Set a fallback template (could contain for example a general message).
         */
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new ItemView(
                new Jsonapi('1.1'),
                new ExampleData(
                    $this->applicationDependencyContainer->getDataExtractionContainer()
                        ->getStrictDataExtractionService()->getNullableString(
                            $request->getAttribute(RoutePartsInterface::ROUTE_PART_3, null),
                        ),
                    $userId,
                ),
                null,
                new ExampleMeta(
                    $this->getCurrentRoute($request),
                    $this->getApiVersionString(),
                ),
            ),
            'api/default',
        );
    }
}
