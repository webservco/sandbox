# Static analisys

## [PHP Parallel Lint](https://packagist.org/packages/php-parallel-lint/php-parallel-lint)

"This application checks the syntax of PHP files in parallel."

```sh
clear && ddev composer check:lint
```

## [Phan](https://packagist.org/packages/phan/phan)

"Phan is a static analyzer for PHP that prefers to minimize false-positives. Phan attempts to prove incorrectness rather than correctness."

```sh
clear && ddev composer check:phan
```

## [PHP_CodeSniffer](https://packagist.org/packages/squizlabs/php_codesniffer)

"PHP_CodeSniffer is a set of two PHP scripts; the main phpcs script that tokenizes PHP, JavaScript and CSS files to detect violations of a defined coding standard, and a second phpcbf script to automatically correct coding standard violations. "

Standards used:

- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [slevomat/coding-standard](https://packagist.org/packages/slevomat/coding-standard)

```sh
clear && ddev composer check:phpcs
```

## [PHPMD](https://packagist.org/packages/phpmd/phpmd)

"PHPMD is a spin-off project of PHP Depend and aims to be a PHP equivalent of the well known Java tool PMD."

```sh
clear && ddev composer check:phpmd
```

## [PHPStan - PHP Static Analysis Tool](https://packagist.org/packages/phpstan/phpstan)

"PHPStan focuses on finding errors in your code without actually running it. It catches whole classes of bugs even before you write tests for the code. It moves PHP closer to compiled languages in the sense that the correctness of each line of the code can be checked before you run the actual line."

```sh
clear && ddev composer check:phpstan
```

## [Psalm](https://packagist.org/packages/vimeo/psalm)

"Psalm is a static analysis tool for finding errors in PHP applications."

```sh
clear && ddev composer check:psalm
```
