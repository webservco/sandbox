# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

@AGENTS.md

## Commands (beyond those in AGENTS.md)

- Auto-fix coding standard issues: `ddev composer fix:phpcs`
- Run a single test: `ddev exec vendor/bin/phpunit --configuration vendor/webservco/coding-standards/phpunit/phpunit-10.xml --filter <TestName>` (the suite reads `tests/Unit/`, which is currently empty; `tests/HTTP/HTTPTests.http` holds manual HTTP requests).
- Lint, PHPCS, PHPStan (level max), PHPMD, Psalm and Phan all run over `bin config public resources src tests`, so templates and route files must pass static analysis too. Rule sets come from `vendor/webservco/coding-standards/`, plus `.phpcs/php-coding-standard.xml` for PHPCS.

## Architecture

The application is assembled from many small `webservco/*` Composer packages (in `vendor/webservco/`). Most base classes (`AbstractDefaultController`, `AbstractDynamicRequestHandler`, `StackHandler`, `RouteMiddleware`, and so on) live there, so read the vendor source when behavior isn't obvious from `src/`. There is no framework kernel or DI autowiring: everything is wired by hand through factories in `src/Project/Factory/`.

### Bootstrapping

`public/index.php` (web) and `bin/SandboxTest.php` (CLI, run via the `bin/sandbox-test` wrapper) share the same initialization: create an `ApplicationDependencyContainer`, load `config/.env.ini` (gitignored; `config/.env.development.ini` is the tracked template), and install the exception handler. After that the web entry point builds a server application through `ApplicationFactoryFactory`, and the CLI builds a command application through `TestCommandFactory`.

### Request pipeline

`Factory/Http/RequestHandlerFactory` builds a PSR-15 `StackHandler`. The middleware order matters: exception handler, view renderer selection (from the Accept header), `RouteMiddleware`, session authentication, request logging, API (JWT) authentication, a second exception handler, then `ResourceMiddleware`. Unmatched requests fall back to `NotFoundController`.

Routing uses three parts: `/{module}/{route}/{extra}`. The default route is `sandbox/test`.
- Part 1 (the module: `api`, `sandbox`, `stuff`) must be listed in `RouteMiddleware` and is mapped in `Factory/Middleware/ResourceMiddlewareFactory` to a module request handler in `src/Project/RequestHandler/Dynamic/`. Each handler also declares the view renderers (HTML/JSON) its module supports.
- Part 2 is looked up in `config/{Module}/Routes.php`, which maps route strings to controller classes. API routes include the version, for example `v1/about`.
- Part 3 is read by controllers via `getServerRequestAttributeService()->getRoutePart(3, $request)`.

### Adding a module or controller

Each module (API, Error, Sandbox, Stuff) has a matching set of pieces:
- a marker interface in `Contract/Controller/` (e.g. `SandboxControllerInterface`);
- an abstract module controller extending `Controller/AbstractController`, which sets the module's main (layout) template;
- a module controller instantiator in `Instantiator/Controller/`, registered in `SpecificModuleControllerInstantiator`. It injects a module-local dependency container from `Factory/Container/`, and Stuff's container also provides form factories. The order of that registration list matters: more specific interfaces go first.

A new controller needs the module interface, a route entry in `config/{Module}/Routes.php`, a `View` class (a readonly DTO extending `AbstractView`), and a template.

### Templates

Templates are plain PHP files in `resources/templates/vanilla/`, resolved by `AbstractController::createTemplateService`. Controllers pass template names without the extension (e.g. `'sandbox/test'`). Directories starting with `_` are special: `_main/` holds layout templates named `main.{module}.{variant}`, `_error/` holds error pages, and `_partial/` holds partials. Templates start with an `assert(...)` on `$view` (and on `$this`, the renderer, when rendering partials) so the static analyzers can type them; follow the docblock pattern in `_main/main.sandbox.default.php`.

### Modules

- **sandbox**: a minimal example (`TestController`) and the `bin/sandbox-test` CLI command.
- **stuff**: session-authenticated CRUD with forms (`webservco/form`) and a MariaDB database. `AuthenticationMiddleware` sets the user id request attribute.
- **api**: a JSON:API endpoint protected by a JWT. `ApiAuthenticationMiddleware` validates against `API_KEY` and `JWT_SECRET` from the config; see `docs/Development/PHP/Sandbox/index.md` for how to build a token.

`src/WebServCo/` is mapped in the PSR-4 autoload but is currently empty.
