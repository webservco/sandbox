# Repository guidance

## Project overview

This repository contains the `webservco/sandbox` PHP application, a small sandbox for modern web development. It requires PHP `^8.4`; the DDEV configuration currently uses PHP 8.5. The web document root is `public/`.

## Layout

- `src/Project/` contains application controllers, request handlers, middleware, factories, views, and commands.
- `config/` contains route configuration; `resources/templates/` contains PHP templates.
- `public/` contains the web entry point and development-only public utilities.
- `bin/` contains CLI entry points; `tests/` contains tests.
- `docs/` contains MkDocs documentation. Read the relevant module docs before changing behavior; `docs/Development/PHP/Sandbox/index.md` describes the sandbox modules and example endpoints.

## Development

- Install dependencies with `ddev composer install` when needed.
- The project is configured for DDEV. Use `ddev start` to start the local environment and `ddev exec` to run commands inside it (for example, `ddev exec bin/sandbox-test`).
- Follow the existing PHP patterns and Composer-managed coding standards when editing application code.
- Run `ddev composer test` for the PHPUnit suite and `ddev composer check` for the configured lint and static-analysis checks. Individual checks are available as `ddev composer check:lint`, `ddev composer check:phpcs`, `ddev composer check:phpstan`, `ddev composer check:phpmd`, `ddev composer check:psalm`, and `ddev composer check:phan`.
- `ddev composer check:skeleton` runs the separate PDS skeleton validation.

## Documentation

The README explains how to serve the MkDocs documentation locally and deploy it. Keep documentation changes consistent with the existing Markdown pages under `docs/`.
