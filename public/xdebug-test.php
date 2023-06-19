<?php

/**
 * Xdebug test.
 */

declare(strict_types=1);

/**
 * @psalm-suppress RiskyCast
 * phpcs:disable SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
 */
$id = array_key_exists('id', $_GET)
? (int) $_GET['id']
: null;

$var1 = 'a';
$var2 = 'b';
$var3 = 'c';

if ($id === null) {
    throw new RuntimeException('Xdebug test');
}

$result = sprintf('%d-%s%s%s', $id, $var1, $var2, $var3);

echo $result;
