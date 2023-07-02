<?php

declare(strict_types=1);

namespace Project\Factory\View\Container\API;

use OutOfBoundsException;
use Project\View\API\APIView;
use UnexpectedValueException;
use WebServCo\View\Contract\ViewContainerFactoryInterface;
use WebServCo\View\Contract\ViewContainerInterface;
use WebServCo\View\Factory\AbstractViewContainerFactory;

use function array_key_exists;
use function is_array;
use function is_int;
use function is_string;
use function sprintf;

/**
 * A general API View Factory.
 */
final class APIViewContainerFactory extends AbstractViewContainerFactory implements ViewContainerFactoryInterface
{
    /**
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $data
     * @phpcs:enable
     */
    public function createViewContainerFromData(array $data): ViewContainerInterface
    {
        $view = new APIView(
            // route
            $this->processStringData('route', $data),
            // result
            $this->processResultData($data),
        );

        /**
         * Do not assume a JSONRendererInterface will be used/ enforced.
         * Set a fallback template (could contain for example a general message).
         */
        return $this->createViewContainerFromView($view, 'api/default');
    }

    /**
     * @phpcs:disable SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint.DisallowedMixedTypeHint
     * @param array<mixed> $data
     * @phpcs:enable
     * @return array<string,int|string|null>
     */
    private function processResultData(array $data): array
    {
        $dataKey = 'result';
        $result = [];
        if (!array_key_exists($dataKey, $data)) {
            throw new OutOfBoundsException(sprintf('Required key not found: "%s".', $dataKey));
        }
        if (!is_array($data[$dataKey])) {
            throw new UnexpectedValueException(sprintf('Unexpected data type for "%s".', $dataKey));
        }
        /**
         * Psalm error: "Unable to determine the type that $.. is being assigned to"
         * However this is indeed mixed, no solution but to suppress error.
         *
         * @psalm-suppress MixedAssignment
         */
        foreach ($data[$dataKey] as $key => $value) {
            if (!is_string($key)) {
                throw new UnexpectedValueException('Unexpected data type for key.');
            }
            $result[$key] = $this->validateIndividualResultItem($value);
        }

        return $result;
    }

    private function validateIndividualResultItem(mixed $value): int|string|null
    {
        if (!is_string($value) && !is_int($value) && $value !== null) {
            throw new UnexpectedValueException('Unexpected data type for value.');
        }

        return $value;
    }
}
