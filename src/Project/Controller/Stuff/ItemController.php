<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Project\View\Stuff\ItemView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\Stuff\DataTransfer\Item\Item;
use WebServCo\Stuff\Entity\Item\ItemEntity;
use WebServCo\View\Contract\ViewContainerInterface;

use function sprintf;

final class ItemController extends AbstractItemController implements StuffControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get mandatory userId.
        $userId = $this->getUserIdFromRequest($request);

        // Get (optional, if edit mode) item id from the request.
        $itemId = $this->getItemIdFromRequest($request);
        // Get (optional, if add mode) parent item id from the request.
        $parentItemId = $this->getParentItemIdFromRequest($request);

        $itemEntity = $this->retrieveNullableItemEntity($itemId, $userId);

        // Create form.
        $form = $this->createAndHandleForm($itemEntity, $request);

        // Handle form
        if ($form->isSent() && $form->isValid()) {
            // Store form data. If new item created, it will generate the new id.
            $itemId = $this->storeItem($form, $itemId, $parentItemId, $userId);

            // Redirect to items page.
            return $this->createLocalRedirectResponse(
                sprintf(
                    '%s/items/%d',
                    RouteInterface::ROUTE,
                    $itemId,
                ),
            );
        }

        // Default response.
        return $this->createResponse(
            $request,
            $this->createViewContainer($form, $itemEntity, $request),
            $form->getResponseStatusCode(),
        );
    }

    private function retrieveNullableItemEntity(?int $itemId, string $userId): ?ItemEntity
    {
        return $itemId !== null
            ? $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
                ->getItemEntityStorage()->retrieveItemEntity($itemId, $userId)
            : null;
    }

    private function createAndHandleForm(?ItemEntity $itemEntity, ServerRequestInterface $request): FormInterface
    {
        $formFactory = $this->getLocalDependencyContainer()->getFormFactoryContainer()->getItemFormFactory();
        if ($itemEntity !== null) {
            $formFactory->setItem($itemEntity->item);
        }
        $form = $formFactory->createForm();
        // Handle request.
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Store item.
     *
     * Returns item id.
     */
    private function storeItem(FormInterface $form, ?int $itemId, ?int $parentItemId, string $userId): int
    {
        if ($itemId !== null) {
            // Update existing item. Only updates DTO data (eg. name and description, no ids).
            $this->getLocalDependencyContainer()->getStorageContainer()->getItemStorageContainer()
                ->getItemStorage()->updateItem($this->createItemObjectFromForm($form), $itemId, $userId);

            return $itemId;
        }

        // Item id is null. Add new item.

        return $this->getLocalDependencyContainer()->getStorageContainer()
            ->getItemStorageContainer()->getItemStorage()->createItem(
                $this->createItemObjectFromForm($form),
                $parentItemId,
                $userId,
            );
    }

    private function createItemObjectFromForm(FormInterface $form): Item
    {
        return new Item(
            $this->applicationDependencyContainer->getDataExtractionContainer()
                ->getStrictNonEmptyDataExtractionService()->getNonEmptyString($form->getField('name')->getValue()),
            $this->applicationDependencyContainer->getDataExtractionContainer()
                ->getStrictDataExtractionService()->getString(
                    $form->getField('description')->getValue(),
                ),
        );
    }

    private function createViewContainer(
        FormInterface $form,
        ?ItemEntity $itemEntity,
        ServerRequestInterface $request,
    ): ViewContainerInterface {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new ItemView(
                $this->createCommonView($request),
                $form,
                $itemEntity,
            ),
            'stuff/item',
        );
    }
}
