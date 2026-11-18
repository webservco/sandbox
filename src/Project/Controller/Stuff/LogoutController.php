<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Override;
use Project\Contract\Controller\StuffControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Session\Model\Enum\FlashMessageType;
use WebServCo\Stuff\Contract\RouteInterface;

use function sprintf;

final class LogoutController extends AbstractStuffController implements StuffControllerInterface
{
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /**
         * Do not check if already logged out;
         * That way we can use this functionality to clear the session and start over,
         * regardless if logged in or not.
         */
        if ((bool) $request->getAttribute('isAuthenticated') === true) {
            $this->applicationDependencyContainer->getServiceContainer()->getLogger('LogoutController')
            ->debug('isAuthenticated');
        }

        // Logout.
        $this->applicationDependencyContainer->getServiceContainer()->getSessionService()->destroy();

        /**
         * Shown on the authentication page. Adding it starts a new session, with a new ID: it holds only the message.
         * The key is written in full, so that the catalog check finds it.
         */
        $this->getLocalDependencyContainer()->getFlashMessageService()->add(
            FlashMessageType::Info,
            'stuff.flash.logged_out',
        );

        // Redirect.
        return $this->createLocalRedirectResponse(sprintf('%s/authenticate', RouteInterface::ROUTE));
    }
}
