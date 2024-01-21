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
        return $this->createResponse(
            $request,
            $this->createViewContainer(
                $this->applicationDependencyContainer->getDataExtractionContainer()
                ->getLooseNonEmptyDataExtractionService()
                ->getNonEmptyNullableInt($request->getAttribute(RoutePartsInterface::ROUTE_PART_3)),
                $request,
            ),
        );
    }

    private function createViewContainer(?int $parentItemId, ServerRequestInterface $request): ViewContainerInterface
    {
        $parentItemEntity = $parentItemId !== null
            ? $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
                ->getItemEntityStorage()->retrieveItemEntity($parentItemId)
            : null;

        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new ItemsView(
                $this->createCommonView($request),
                $this->getLocalDependencyContainer()->getStorageContainer()
                ->getItemStorageContainer()->getItemEntityStorage()->iterateItemEntity($parentItemId),
                $parentItemEntity,
                $parentItemEntity !== null
                ? $this->getLocalDependencyContainer()->getStorageContainer()
                ->getItemStorageContainer()->getItemEntityStorage()->iterateItemEntityUpstream($parentItemEntity)
                : null,
            ),
            'stuff/items',
        );
    }
}
