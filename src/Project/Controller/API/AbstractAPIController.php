<?php

declare(strict_types=1);

namespace Project\Controller\API;

use Project\Controller\AbstractController;
use Project\Middleware\API\ApiAuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;
use WebServCo\Form\Contract\FormInterface;
use WebServCo\JSONAPI\Contract\Service\Container\APILocalServiceContainerInterface;
use WebServCo\JSONAPI\Contract\Service\JSONAPIHandlerInterface;
use WebServCo\JSONAPI\DataTransfer\Document\JSONAPI;
use WebServCo\JSONAPI\DataTransfer\Errors\DefaultError;
use WebServCo\JSONAPI\View\ItemView;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Contract\ViewInterface;

use function array_merge;
use function sprintf;

/**
 * An abstract controller with dependencies specific to current module.
 */
abstract class AbstractAPIController extends AbstractController
{
    protected function createErrorView(JSONAPIHandlerInterface $handler): ViewInterface
    {
        /**
         * This is used when the request is not valid.
         */
        if ($handler->isSent() && $handler->isValid()) {
            // Sanity check, should enver arrive here.
            throw new UnexpectedValueException('Form is valid');
        }

        // Form level error messages.
        $generalErrors = $this->processErrorViewFormGeneralErrorMessages($handler);

        // Field level error messages
        $fieldErrors = $this->processErrorViewFormItemErrorMessages($handler);

        return new ItemView(new JSONAPI('1.1'), null, array_merge($generalErrors, $fieldErrors), null);
    }

    protected function createErrorViewContainer(JSONAPIHandlerInterface $handler): ViewContainerInterface
    {
        /**
         * Do not simply assume a JSONRendererInterface will be used / enforced.
         * Set a fallback template (could contain for example a general message).
         */
        return $this->viewServicesContainer->getViewContainerFactory()->createViewContainerFromView(
            $this->createErrorView($handler),
            'api/default',
        );
    }

    protected function createMainViewContainer(
        ServerRequestInterface $request,
        ViewContainerInterface $viewContainer,
    ): ViewContainerInterface {
        return $this->createMainViewContainerWithTemplate($request, 'main/main.api.default', $viewContainer);
    }

    protected function getApiVersionString(): string
    {
        return $this->applicationDependencyContainer->getServiceContainer()->getConfigurationGetter()
            ->getString('API_VERSION');
    }

    protected function getCurrentRoute(ServerRequestInterface $request): ?string
    {
        return $this->applicationDependencyContainer->getRequestServiceContainer()
            ->getServerRequestAttributeService()->getRoutePart(2, $request);
    }

    /**
     * Return local implementation of LocalDependencyContainerInterface
     */
    protected function getLocalDependencyContainer(): APILocalServiceContainerInterface
    {
        if (!$this->localDependencyContainer instanceof APILocalServiceContainerInterface) {
            throw new UnexpectedValueException('Invalid instance.');
        }

        return $this->localDependencyContainer;
    }

    protected function getUserIdFromRequest(ServerRequestInterface $request): string
    {
        $userId = $this->applicationDependencyContainer->getDataExtractionContainer()
            ->getStrictDataExtractionService()->getNullableString(
                $request->getAttribute(ApiAuthenticationMiddleware::REQUEST_ATTRIBUTE_KEY_USER_ID, null),
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

    protected function initializeJSONAPIHandler(): JSONAPIHandlerInterface
    {
        $handlerFactory = $this->getLocalDependencyContainer()->getJsonApiServiceContainer()
            ->getDefaultHandlerFactory();

        return $handlerFactory->createHandler();
    }

    /**
     * @return array<int,\WebServCo\JSONAPI\Contract\Errors\ErrorInterface>
     */
    private function processErrorViewFormGeneralErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrorMessages() as $errorMessage) {
            $errors[] = new DefaultError(
                '400',
                'Input error',
                sprintf('General: %s', $errorMessage),
            );
        }

        return $errors;
    }

    /**
     * @return array<int,\WebServCo\JSONAPI\Contract\Errors\ErrorInterface>
     */
    private function processErrorViewFormItemErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getFields() as $field) {
            foreach ($field->getErrorMessages() as $errorMessage) {
                $errors[] = new DefaultError(
                    '400',
                    'Input error',
                    sprintf('%s: %s', $field->getName(), $errorMessage),
                );
            }
        }

        return $errors;
    }
}
