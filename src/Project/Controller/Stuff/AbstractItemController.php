<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractItemController extends AbstractStuffController implements StuffControllerInterface
{
    protected function getItemIdFromRequest(ServerRequestInterface $request): ?int
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
        ->getLooseNonEmptyDataExtractionService()
        ->getNonEmptyNullableInt(
            $this->applicationDependencyContainer->getRequestServiceContainer()
                ->getServerRequestAttributeService()->getRoutePart(3, $request),
        );
    }

    protected function getParentItemIdFromRequest(ServerRequestInterface $request): ?int
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getLooseArrayNonEmptyDataExtractionService()
            ->getNonEmptyNullableInt($request->getQueryParams(), 'parent', null);
    }
}
