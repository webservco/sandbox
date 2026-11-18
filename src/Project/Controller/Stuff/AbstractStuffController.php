<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Override;
use Project\Contract\Container\Stuff\StuffLocalServiceContainerInterface;
use Project\Controller\AbstractController;
use Project\Middleware\AuthenticationMiddleware;
use Project\View\Stuff\MainView;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\View\Contract\ViewContainerInterface;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractStuffController extends AbstractController
{
    #[Override]
    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createStuffMainViewContainer($request, '_main/main.stuff.pico', $viewContainer);
    }

    /**
     * Main view (layout): the rendered page, then the flash messages, taken from the session once the page is
     * rendered; and the CSRF token of the search form (every page of the module has a session).
     *
     * Only for HTML: a JSON response or a redirect leaves the flash messages waiting.
     */
    protected function createStuffMainViewContainer(
        ServerRequestInterface $request,
        string $templateName,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        $data = $this->viewServicesContainer->getViewRenderer()->renderViewContainer($viewContainer);

        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            new MainView(
                $this->getLocalizationContainer($request),
                $this->createCommonView($request),
                $this->getLocalDependencyContainer()->getCsrfTokenService()->getToken(),
                $data,
                $this->getLocalDependencyContainer()->getFlashMessageService()->take(),
            ),
            $templateName,
        );
    }

    /**
     * Return local implementation of LocalDependencyContainerInterface
     */
    #[Override]
    protected function getLocalDependencyContainer(): StuffLocalServiceContainerInterface
    {
        if (!$this->localDependencyContainer instanceof StuffLocalServiceContainerInterface) {
            throw new UnexpectedValueException('Invalid instance.');
        }

        return $this->localDependencyContainer;
    }

    protected function getUserIdFromRequest(ServerRequestInterface $request): string
    {
        $userId = $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getStrictDataExtractionService()->getNullableString(
                $request->getAttribute(AuthenticationMiddleware::REQUEST_ATTRIBUTE_KEY_USER_ID, null),
            );

        /**
         * Sanity check, should never arrive here;
         * userId is set by ApiAuthenticationMiddleware, and we do not arrive here if not set (not authenticated)
         */
        if ($userId === null) {
            throw new UnexpectedValueException('User id not set.');
        }

        return $userId;
    }
}
