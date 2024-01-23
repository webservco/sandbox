<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Project\Middleware\AuthenticationMiddleware;
use Project\View\Stuff\AuthenticationView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\Contract\ViewContainerInterface;

use function sprintf;

final class AuthenticationController extends AbstractStuffController implements StuffControllerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Check if already authenticated
        if ((bool) $request->getAttribute('isAuthenticated') === true) {
            // Redirect.
            return $this->createLocalRedirectResponse(sprintf('%s/items', RouteInterface::ROUTE));
        }

        // Create form.
        $form = $this->createAndHandleForm($request);

        // Handle form.
        if ($form->isSent() && $form->isValid()) {
            // Set authenticated.
            $this->setAuthenticated();

            // Get next URL.
            $nextLocation = $this->getNextLocation();

            // Clear next URL.
            $this->clearNextLocation();

            // Redirect.
            return $this->createLocalRedirectResponse($nextLocation);
        }

        // Default response.
        return $this->createResponse(
            $request,
            $this->createViewContainer($form, $request),
            $form->getResponseStatusCode(),
        );
    }

    /**
     * Override.
     *
     * Use a custom template for this page.
     */
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate(
            $request,
            'main/main.stuff.notauthenticated.pico',
            $viewContainer,
        );
    }

    private function clearNextLocation(): bool
    {
        return $this->applicationDependencyContainer->getServiceContainer()->getSessionService()
        ->unsetSessionDataItem(AuthenticationMiddleware::NEXT_LOCATION_KEY);
    }

    private function createAndHandleForm(ServerRequestInterface $request): FormInterface
    {
        $formFactory = $this->getLocalDependencyContainer()->getFormFactoryContainer()->getAuthenticationFormFactory();
        $form = $formFactory->createForm();
        // Handle request.
        $form->handleRequest($request);

        return $form;
    }

    private function createViewContainer(FormInterface $form, ServerRequestInterface $request): ViewContainerInterface
    {
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new AuthenticationView(
                $this->createCommonView($request),
                $form,
            ),
            'stuff/authentication',
        );
    }

    private function getNextLocation(): string
    {
        return $this->applicationDependencyContainer->getDataExtractionContainer()
        ->getStrictArrayNonEmptyDataExtractionService()->getNonEmptyString(
            $this->applicationDependencyContainer->getServiceContainer()->getSessionService()->getSessionData(),
            AuthenticationMiddleware::NEXT_LOCATION_KEY,
            sprintf('%s/items', RouteInterface::ROUTE),
        );
    }

    private function setAuthenticated(): bool
    {
        return $this->applicationDependencyContainer->getServiceContainer()->getSessionService()
        ->setSessionDataItem('isAuthenticated', true);
    }
}
