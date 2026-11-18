<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Fig\Http\Message\RequestMethodInterface;
use Override;
use Project\Contract\Controller\StuffControllerInterface;
use Project\View\Stuff\ItemDeleteView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Session\Model\Enum\FlashMessageType;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\Stuff\Entity\Item\ItemEntity;

use function sprintf;

/**
 * Deletes an item, after a confirmation: one URL for both steps.
 *
 * - GET: the confirmation page, with a POST form to the same URL (the CSRF token only).
 * - POST: the deletion, a flash message, and a redirect to the list the item was in. The CSRF middleware has checked
 *   the token.
 */
final class ItemDeleteController extends AbstractItemController implements StuffControllerInterface
{
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get mandatory userId.
        $userId = $this->getUserIdFromRequest($request);

        // Get (mandatory) item id from the request.
        $itemId = $this->getItemIdFromRequest($request);
        if ($itemId === null) {
            throw new UnexpectedValueException('Item id is empty.');
        }

        // Throws if the user has no such item.
        $itemEntity = $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
            ->getItemEntityStorage()->retrieveItemEntity($itemId, $userId);

        if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
            return $this->createResponse(
                $request,
                $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
                    new ItemDeleteView(
                        $this->createCommonView($request),
                        $this->getLocalDependencyContainer()->getCsrfTokenService()->getToken(),
                        $itemEntity,
                    ),
                    'stuff/item_delete',
                ),
            );
        }

        return $this->deleteItem($itemEntity, $userId);
    }

    private function deleteItem(ItemEntity $itemEntity, string $userId): ResponseInterface
    {
        // Delete.
        $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
        ->getItemStorage()->deleteItem($itemEntity->id, $userId);

        // Shown on the next page. The key is written in full, so that the catalog check finds it.
        $this->getLocalDependencyContainer()->getFlashMessageService()->add(
            FlashMessageType::Success,
            'stuff.flash.item_deleted',
            ['name' => $itemEntity->item->name],
        );

        // Redirect to the list the item was in.
        return $this->createLocalRedirectResponse(
            sprintf(
                '%s/items%s',
                RouteInterface::ROUTE,
                $itemEntity->parentItemId !== null ? sprintf('/%d', $itemEntity->parentItemId) : '',
            ),
        );
    }
}
