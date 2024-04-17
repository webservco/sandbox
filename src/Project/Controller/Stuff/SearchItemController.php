<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Project\View\Stuff\SearchItemView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\Contract\ViewContainerInterface;

use function sprintf;

final class SearchItemController extends AbstractStuffController implements StuffControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get mandatory userId.
        $userId = $this->getUserIdFromRequest($request);

        // Create form.
        $form = $this->createAndHandleForm($request);

        // Handle form
        if ($form->isSent() && $form->isValid()) {
            return $this->createResponse(
                $request,
                $this->createViewContainer($form, $request, $userId),
            );
        }

        // Default response.
        return $this->createLocalRedirectResponse(sprintf('%s/items', RouteInterface::ROUTE));
    }

    private function createAndHandleForm(ServerRequestInterface $request): FormInterface
    {
        $form = $this->getLocalDependencyContainer()->getFormFactoryContainer()->getSearchItemFormFactory()
        ->createForm();
        // Handle request.
        $form->handleRequest($request);

        return $form;
    }

    private function createViewContainer(
        FormInterface $form,
        ServerRequestInterface $request,
        string $userId,
    ): ViewContainerInterface {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new SearchItemView(
                $this->createCommonView($request),
                $form,
                $this->getLocalDependencyContainer()->getStorageContainer()
                ->getItemStorageContainer()->getSearchItemStorage()->iterateItemEntity(
                    $this->applicationDependencyContainer->getDataExtractionContainer()
                    ->getStrictNonEmptyDataExtractionService()->getNonEmptyString(
                        $form->getField('search_item')->getValue(),
                    ),
                    $userId,
                ),
            ),
            'stuff/search_item',
        );
    }
}
