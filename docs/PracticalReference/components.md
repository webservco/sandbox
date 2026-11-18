# PHP: WebServCo component reference

## Integration model

The application composes small WebServCo packages through Composer. The `WebServCo\` namespace contains the component APIs and implementations; `Project\` contains app-specific wiring and behavior. PSR interfaces define key seams, especially PSR-7 messages, PSR-15 middleware and handlers, PSR-3 logging, and PSR-17 factories.

Prefer component contracts at application boundaries and concrete implementations inside factories. A new feature should first reuse an installed component interface and service where it fits, then add project-specific code at the nearest app boundary.

The following table summarizes the WebServCo runtime components visible in the installed dependency tree and how the sandbox uses them. `composer.json` lists the direct package requirements; some components below are installed transitively.

| Package | Responsibility in this application |
| --- | --- |
| `webservco/app-stuff-common` | Shared Stuff route name, item DTO/entity, storage contracts, containers, and prepared SQL for the example item feature. |
| `webservco/application` | Application lifecycle and runner contracts. |
| `webservco/application-default` | Default server and command application implementations. |
| `webservco/application-runner` | Connects a PSR request handler to the SAPI response emitter. |
| `webservco/command` | Command factory/runner and output contracts/services. |
| `webservco/configuration` | INI/PHP configuration loading and typed configuration access. |
| `webservco/controller` | Controller contracts, module-specific instantiation, reflection validation, and default controller response/view helpers. |
| `webservco/data` | DTO marker and strict/loose data-extraction services. |
| `webservco/database` | PDO configuration/container/factory and query helpers. |
| `webservco/dependency-container` | Application, service, factory, request, and response containers. |
| `webservco/emitter` | SAPI and stack emitters used to send the response. |
| `webservco/environment` | Environment identifiers used to control error detail. |
| `webservco/error` | PHP error handling service initialized by the default application. |
| `webservco/exception` | Logged application and uncaught exception handlers. |
| `webservco/form` | Form, field, filter, validator, and POST handling contracts and implementations. |
| `webservco/http` | PSR HTTP message factories, server-request creation, and request/response service contracts. |
| `webservco/http-request-handler` | Middleware stack, dynamic module handlers, and exception/fallback handlers. |
| `webservco/http-request-service` | Request method/body/header and server-attribute services used by containers and API handling. |
| `webservco/http-response-service` | Response status-code service. |
| `webservco/i18n` | Languages, message catalogs and their checks, ICU translator and formatter, the language of a request (`LanguageMiddleware`), localized views and the language switch. |
| `webservco/jsonapi` | JSON:API document, data, and error contracts/DTOs. |
| `webservco/jsonapi-application` | JSON:API handler, request validation, service containers, and API view types. |
| `webservco/jwt` | HS256 JWT decoder and normalized payload DTO. |
| `webservco/log` | Context logger factory and log services. |
| `webservco/middleware` | Routing, resource dispatch, exception, logging, and renderer-selection middleware; the session and CSRF middleware. |
| `webservco/route` | Route configuration contract, value object, and PHP route-file loader. |
| `webservco/reflection` | Reflection factories/services used to inspect controller interfaces and constructor parameters. |
| `webservco/session` | Session configuration, cookie settings, and session service; the session store, the CSRF token and the flash messages. |
| `webservco/stopwatch` | Lap timer and statistics used during startup, request handling, and commands. |
| `webservco/view` | View contracts, view containers, renderer selection, HTML/JSON renderers, and template rendering. |

The runtime package constraints are in `composer.json`. Development tools and their project configuration are described in [Coding standards and development](coding-standards.md).

## Component boundaries used by the app

### Application and dependency containers

`ApplicationDependencyContainer` is created once per bootstrap and lazily provides application-wide services. The project passes it into:

- `ApplicationFactoryFactory`, which builds a default server application;
- module local-container factories, which add dependencies to a controller's local container;
- command factories, which construct command dependencies.

`ServiceContainer` lazily creates the configuration getter and session service, caches loggers and output services by channel, and exposes the injected lap timer. The application dependency container also lazily creates the data-extraction, factory, request, response, and service containers. Module containers cache their own factories and storage services. This keeps object creation in composition code, while controllers use injected containers.

### Controllers and route configuration

The route loader turns arrays of route keys and class names into `RouteConfiguration` objects. The controller instantiator checks the class and its interfaces, selects the module-specific instantiator from the marker interface, and creates a view-services container with a renderer selected for the request.

An application controller generally:

- implements the marker interface for its module;
- extends `Project\Controller\AbstractController` directly or the module's abstract controller;
- exposes `handle(ServerRequestInterface): ResponseInterface`;
- creates a typed view and a `ViewContainer`;
- returns a response or a redirect.

### Views and templates

The `ViewInterface` is the data boundary between a controller and renderer. App views extend `AbstractView`, whose `escape()` method is intended for HTML escaping. `ViewContainer` combines a view object with a template name and later receives a `TemplateService`.

The HTML renderer includes the requested PHP template in a scoped render method with `$view` available. The JSON renderers serialize the view object. The base controller wraps only HTML views in a `MainView`; non-HTML data is rendered directly.

### Languages and translations

`webservco/i18n` puts the Sandbox module's pages in the language of the request; its README (`vendor/webservco/i18n/README.md`) has the catalog format, the message rules and the formats. The other modules aren't translated, except the Stuff module's flash messages ([Session, CSRF token and flash messages](#session-csrf-token-and-flash-messages)): its pages stay in English.

- **Languages.** The enum `Project\Enum\I18n\Language` implements `LanguageInterface`: English (the default, ICU locale `en_GB`) and German, in the order of the language switch. `LanguageListFactory` is the one place that builds the `LanguageList`.
- **Language of a request.** `LanguageMiddleware` runs first in the stack, for every module. It takes the language from `?lang=`, the `lang` cookie, `Accept-Language` or the default, in that order, and puts it on the request. Every response gets `Content-Language`; a `?lang=` response sets the cookie, the others vary on `Cookie, Accept-Language`.
- **Translator and formatter.** `AbstractController::getLocalizationContainer()` creates them once per request through `RequestLocalizationContainerFactory`: the catalogs of `resources/i18n/`, the request's language, and the display time zone (`DISPLAY_TIME_ZONE`, UTC).
- **Views.** A localized view extends `AbstractLocalizedView` and takes the container as its first constructor argument. Its template calls `text()`, `date()`, `time()`, `dateTime()` and `number()`, which return escaped text. The module's layout view is `Project\View\Sandbox\MainView` instead of `WebServCo\View\View\MainView`; it carries the language switch, from `LanguageLinkViewFactory`.
- **Catalogs.** `resources/i18n/{code}/{catalog}.php` returns `name => message`; the key is `{catalog}.{name}`. English is the reference and has every key; a key German lacks falls back to English with a warning in the log.
- **Checks.** `tests/Unit/Resources/I18n/CatalogTest.php` runs the component's eight catalog checks: structure, message syntax, lookalike letters, parameters, plural categories, duplicate cases, British spelling, and every literal key in `config`, `resources/templates` and `src`.

### Session, CSRF token and flash messages

The Stuff module uses the session store, the CSRF token and the flash messages of `webservco/session`, and the session and CSRF middleware of `webservco/middleware`. Their READMEs (`vendor/webservco/session/README.md`, `vendor/webservco/middleware/README.md`) describe them in full.

- **Session store.** `Factory/Session/SessionStoreFactory` creates it on the application container's session service, with the session files in `var/sessions` (in git only with its `.gitignore`; writable by the web server). A read never starts a session, a write does. `AuthenticationMiddleware` still starts the session itself for every Stuff request, in the same directory (`createStoragePath()`): a session started elsewhere couldn't be resumed.
- **Session middleware.** Right after the route middleware: it resumes the session of a request that sends the session cookie, and sends `Cache-Control: private, no-cache` with every response to a request with a session, error pages included. A visitor of the Sandbox module gets no session.
- **CSRF middleware.** Right after the second exception handler: every request that isn't GET, HEAD or OPTIONS needs the session's token, in the form field `csrf_token` or the header `X-CSRF-Token`, in every module except `api` (it uses a JWT). A missing or wrong token gets the error page with 403 and "This page has expired.": `SessionMiddlewareFactory` gives the middleware a `Project\Exception\Http\ClientErrorException`, which `ErrorController` shows in every environment. The refusal is logged as a warning, with the method and the path.
- **Token and flash message services.** The Stuff local container gives them (`getCsrfTokenService()`, `getFlashMessageService()`). Every Stuff form has the hidden token field: the login form, the item form and the layout's search form.
- **Login and logout.** A valid login renews the session ID (`regenerateId()`) and the token (`renewToken()`), so that neither works after the login if it was known before, then adds "You are logged in.". Logout destroys the session, then adds "You are logged out.", which starts a new session that holds only the message.
- **Flash messages.** Stored as type, catalog key and parameters, and taken from the session by the module's layout once the page is rendered (`AbstractStuffController::createStuffMainViewContainer()`, `Project\View\Stuff\MainView`). The layout translates them in the language of the request (catalog `stuff`), each in an `<article role="status">` with its `lang`. A JSON response or a redirect leaves them waiting.

### Input and data transfer

The data extraction container exposes both strict and loose services, with scalar and non-empty variants and array-aware variants:

- use **strict** extraction when the value must already have the expected type;
- use **loose** extraction where scalar coercion is appropriate, such as a route string interpreted as an optional integer;
- use a **non-empty** service when empty values are invalid.

Forms own filtering and validation of submitted field data. Convert valid form values into a DTO before calling a storage or domain service. The Stuff `Item` DTO contains the name and description; `ItemEntity` represents a persisted item with its id and parent relationship.

### Storage

The project Stuff local container creates the MySQL PDO container from typed configuration, then supplies the shared `StuffStorageContainer`. Storage methods use prepared statements, hydrate rows into DTO/entity objects, and include user ownership checks in item queries.

The database schema is outside this repository. The shared library assumes a `stuff_item` table and related columns; this app has no migration tooling or schema definition checked in.

## Useful source files

| Concern | Project wiring | Component implementation to understand |
| --- | --- | --- |
| Server app | `src/Project/Factory/Application/ApplicationFactoryFactory.php`, `public/index.php` | `application-default`, `application-runner` |
| Middleware stack | `src/Project/Factory/Http/RequestHandlerFactory.php` | `http-request-handler`, `middleware` |
| Route dispatch | `src/Project/Factory/Middleware/ResourceMiddlewareFactory.php` | `route`, dynamic request handlers |
| Controller creation | `src/Project/Instantiator/Controller/` | `controller`, `reflection` |
| Page rendering | `src/Project/Controller/AbstractController.php`, `src/Project/View/` | `view` |
| Languages | `src/Project/Enum/I18n/`, `src/Project/Factory/I18n/`, `src/Project/Controller/AbstractController.php`, `resources/i18n/` | `i18n` |
| Session, CSRF, flash messages | `src/Project/Factory/Session/`, `src/Project/Factory/Middleware/SessionMiddlewareFactory.php`, `src/Project/Controller/Stuff/`, `src/Project/View/Stuff/MainView.php` | `session`, `middleware` |
| Forms | `src/Project/Factory/Form/`, `src/Project/Controller/Stuff/` | `form` |
| API request | `src/Project/Middleware/API/ApiAuthenticationMiddleware.php`, `src/Project/Controller/API/` | `jwt`, `jsonapi`, `jsonapi-application` |
| Stuff persistence | `src/Project/Factory/Container/StuffLocalDependencyContainerFactory.php` | `app-stuff-common`, `database`, `data` |
| CLI | `bin/SandboxTest.php`, `src/Project/Factory/Command/` | `command`, `application-default` |
