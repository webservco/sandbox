# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

@AGENTS.md

## Commands (beyond those in AGENTS.md)

- Auto-fix coding standard issues: `ddev composer fix:phpcs`
- Run a single test: `ddev exec vendor/bin/phpunit --configuration vendor/webservco/coding-standards/phpunit/phpunit-13.xml --no-coverage --filter <TestName>` (the suite reads `tests/Unit/`; `tests/HTTP/HTTPTests.http` holds manual HTTP requests).
- Lint, PHPCS, PHPStan (level max), PHPMD, Psalm and Phan all run over `bin config public resources src tests`, so templates and route files must pass static analysis too. Rule sets come from `vendor/webservco/coding-standards/`, plus `.phpcs/php-coding-standard.xml` for PHPCS.

## Architecture

The application is assembled from many small `webservco/*` Composer packages (in `vendor/webservco/`). Most base classes (`AbstractDefaultController`, `AbstractDynamicRequestHandler`, `StackHandler`, `RouteMiddleware`, and so on) live there, so read the vendor source when behavior isn't obvious from `src/`. There is no framework kernel or DI autowiring: everything is wired by hand through factories in `src/Project/Factory/`.

### Bootstrapping

`public/index.php` (web) and `bin/SandboxTest.php` (CLI, run via the `bin/sandbox-test` wrapper) share the same initialization through `DefaultApplicationInitializer` (`webservco/application-default`): create an `ApplicationDependencyContainer`, load `config/.env.ini` (gitignored; `config/.env.development.ini` is the tracked template), and install the exception handler. After that the web entry point builds a server application through `ApplicationFactoryFactory`, and the CLI builds a command application through `TestCommandFactory`.

### Request pipeline

`Factory/Http/RequestHandlerFactory` builds a PSR-15 `StackHandler`. The middleware order matters: language (`LanguageMiddleware`), exception handler, view renderer selection (from the Accept header), `RouteMiddleware`, session (`SessionMiddleware`), session authentication, request logging, API (JWT) authentication, a second exception handler, the CSRF check (`CsrfMiddleware`), then `ResourceMiddleware`. Unmatched requests fall back to `NotFoundController`.

Routing uses three parts: `/{module}/{route}/{extra}`. The default route is `sandbox/test`.
- Part 1 (the module: `api`, `sandbox`, `stuff`) must be listed in `RouteMiddleware` and is mapped in `Factory/Middleware/ResourceMiddlewareFactory` to a module request handler in `src/Project/RequestHandler/Dynamic/`. Each handler also declares the view renderers (HTML/JSON) its module supports.
- Part 2 is looked up in `config/{Module}/Routes.php`, which maps route strings to controller classes. API routes include the version, for example `v1/about`.
- Part 3 is read by controllers via `getServerRequestAttributeService()->getRoutePart(3, $request)`.

### Languages

The Sandbox module's pages are in English (the default) or German, with `webservco/i18n` (its README is in `vendor/webservco/i18n/`). `LanguageMiddleware` resolves the language of every request (`?lang=`, the `lang` cookie, `Accept-Language`) and sets `Content-Language`. The languages are the enum `Project\Enum\I18n\Language`; `Factory/I18n/LanguageListFactory` is the one place that builds their list. `AbstractController::getLocalizationContainer()` gives the translator and formatter of the request; the module's views extend `AbstractLocalizedView`, and its layout view is `Project\View\Sandbox\MainView`, with the language switch. The Stuff module's pages are in English only, but its flash messages are in the language of the request (catalog `stuff`). Texts are keys in `resources/i18n/{code}/{catalog}.php`; English has every key. `tests/Unit/Resources/I18n/CatalogTest.php` runs the component's catalog checks.

### Adding a module or controller

Each module (API, Error, Sandbox, Stuff) has a matching set of pieces:
- a marker interface in `Contract/Controller/` (e.g. `SandboxControllerInterface`);
- an abstract module controller extending `Controller/AbstractController`, which sets the module's main (layout) template;
- a module controller instantiator in `Instantiator/Controller/`, registered in `SpecificModuleControllerInstantiator`. It injects a module-local dependency container from `Factory/Container/`, and Stuff's container also provides form factories. The order of that registration list matters: more specific interfaces go first.

A new controller needs the module interface, a route entry in `config/{Module}/Routes.php`, a `View` class (a readonly DTO extending `AbstractView`), and a template.

### Templates

Templates are plain PHP files in `resources/templates/vanilla/`, resolved by `AbstractController::createTemplateService`. Controllers pass template names without the extension (e.g. `'sandbox/test'`). Directories starting with `_` are special: `_main/` holds layout templates named `main.{module}.{variant}`, `_error/` holds error pages, and `_partial/` holds partials. Templates start with an `assert(...)` on `$view` (and on `$this`, the renderer, when rendering partials) so the static analyzers can type them; follow the docblock pattern in `_main/main.sandbox.default.php`.

### Modules

- **sandbox**: a minimal example (`TestController`) in English and German, and the `bin/sandbox-test` CLI command.
- **stuff**: session-authenticated CRUD with forms (`webservco/form`) and a MariaDB database. `AuthenticationMiddleware` starts the session and sets the user id request attribute. The session store, the CSRF token and the flash messages are `webservco/session`'s (wiring: `Factory/Session/SessionStoreFactory`, session files in `var/sessions`; `Factory/Middleware/SessionMiddlewareFactory`; the module's container gives the token and flash message services). Every form has the token's hidden field; login renews the session ID and the token, and login and logout add a flash message, shown by the module's layout view `Project\View\Stuff\MainView`. A refused token gets the error page with 403 (`Project\Exception\Http\ClientErrorException`, shown in every environment).
- **api**: a JSON:API endpoint protected by a JWT. `ApiAuthenticationMiddleware` validates against `API_KEY` and `JWT_SECRET` from the config; see `docs/Development/PHP/Sandbox/index.md` for how to build a token.

`src/WebServCo/` is mapped in the PSR-4 autoload but is currently empty.
