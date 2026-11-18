<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Override;
use Project\Contract\Controller\StuffControllerInterface;
use Project\Middleware\AuthenticationMiddleware;
use Project\View\Stuff\AuthenticationView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\Session\Model\Enum\FlashMessageType;
use WebServCo\Stuff\Contract\RouteInterface;
use WebServCo\View\Contract\ViewContainerInterface;

use function md5;
use function sprintf;
use function substr;

final class AuthenticationController extends AbstractStuffController implements StuffControllerInterface
{
    #[Override]
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
            /**
             * A new session ID and a new CSRF token: the ones known before the login don't work after it
             * (session fixation).
             */
            $this->applicationDependencyContainer->getServiceContainer()->getSessionService()->regenerateId();
            $this->getLocalDependencyContainer()->getCsrfTokenService()->renewToken();

            // Set authenticated.
            $this->setAuthenticated($this->generateUserId());

            // Get next URL.
            $nextLocation = $this->getNextLocation();

            // Clear next URL.
            $this->clearNextLocation();

            // Shown on the next page. The key is written in full, so that the catalog check finds it.
            $this->getLocalDependencyContainer()->getFlashMessageService()->add(
                FlashMessageType::Success,
                'stuff.flash.logged_in',
            );

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
    #[Override]
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createStuffMainViewContainer($request, '_main/main.stuff.notauthenticated.pico', $viewContainer);
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
                $this->getLocalDependencyContainer()->getCsrfTokenService()->getToken(),
                $form,
            ),
            'stuff/authentication',
        );
    }

    private function generateUserId(): string
    {
        // Generate user id based on the password from the configuration
        return substr(
            md5($this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
                ->getString('AUTHENTICATION_PASSWORD')),
            0,
            15,
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

    private function setAuthenticated(string $userId): bool
    {
        $this->applicationDependencyContainer->getServiceContainer()->getSessionService()
            ->setSessionDataItem('userId', $userId);

        return $this->applicationDependencyContainer->getServiceContainer()->getSessionService()
            ->setSessionDataItem('isAuthenticated', true);
    }
}
