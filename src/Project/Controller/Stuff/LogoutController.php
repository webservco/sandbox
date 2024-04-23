<?php

declare(strict_types=1);

namespace Project\Controller\Stuff;

use Project\Contract\Controller\StuffControllerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebServCo\Stuff\Contract\RouteInterface;

use function sprintf;

final class LogoutController extends AbstractStuffController implements StuffControllerInterface
{
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

        // Redirect.
        return $this->createLocalRedirectResponse(sprintf('%s/authenticate', RouteInterface::ROUTE));
    }
}
