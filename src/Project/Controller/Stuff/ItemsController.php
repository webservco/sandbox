<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Project\View\Stuff\ItemsView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Route\Contract\ThreePart\RoutePartsInterface;
use WebServCo\View\Contract\ViewContainerInterface;

final class ItemsController extends AbstractStuffController implements StuffControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get mandatory userId.
        $userId = $this->getUserIdFromRequest($request);

        return $this->createResponse(
            $request,
            $this->createViewContainer(
                $this->applicationDependencyContainer->getDataExtractionContainer()
                ->getLooseNonEmptyDataExtractionService()
                ->getNonEmptyNullableInt($request->getAttribute(RoutePartsInterface::ROUTE_PART_3)),
                $request,
                $userId,
            ),
        );
    }

    private function createViewContainer(
        ?int $parentItemId,
        ServerRequestInterface $request,
        string $userId,
    ): ViewContainerInterface {
        $parentItemEntity = $parentItemId !== null
            ? $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
                ->getItemEntityStorage()->retrieveItemEntity($parentItemId, $userId)
            : null;

        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new ItemsView(
                $this->createCommonView($request),
                $this->getLocalDependencyContainer()->getStorageContainer()
                ->getItemStorageContainer()->getItemEntityStorage()->iterateItemEntity($parentItemId, $userId),
                $parentItemEntity,
                $parentItemEntity !== null
                ? $this->getLocalDependencyContainer()->getStorageContainer()
                ->getItemStorageContainer()->getItemEntityStorage()->iterateItemEntityUpstream(
                    $parentItemEntity,
                    $userId,
                )
                : null,
            ),
            'stuff/items',
        );
    }
}
