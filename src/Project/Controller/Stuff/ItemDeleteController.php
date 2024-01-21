<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Stuff\Contract\RouteInterface;

use function sprintf;

final class ItemDeleteController extends AbstractItemController implements StuffControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get (mandatory) item id from the request.
        $itemId = $this->getItemIdFromRequest($request);
        if ($itemId === null) {
            throw new UnexpectedValueException('Item id is empty.');
        }
        // Get (optional) parent item id from the request.
        $parentItemId = $this->getParentItemIdFromRequest($request);

        // Delete.
        $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
        ->getItemStorage()->deleteItem($itemId);

        // Redirect.
        return $this->createLocalRedirectResponse(
            sprintf(
                '%s/items%s',
                RouteInterface::ROUTE,
                $parentItemId !== null ? sprintf('/%d', $parentItemId) : '',
            ),
        );
    }
}
