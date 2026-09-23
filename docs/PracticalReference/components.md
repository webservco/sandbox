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
| `webservco/jsonapi` | JSON:API document, data, and error contracts/DTOs. |
| `webservco/jsonapi-application` | JSON:API handler, request validation, service containers, and API view types. |
| `webservco/jwt` | HS256 JWT decoder and normalized payload DTO. |
| `webservco/log` | Context logger factory and log services. |
| `webservco/middleware` | Routing, resource dispatch, exception, logging, and renderer-selection middleware. |
| `webservco/route` | Route configuration contract, value object, and PHP route-file loader. |
| `webservco/reflection` | Reflection factories/services used to inspect controller interfaces and constructor parameters. |
| `webservco/session` | Session configuration, cookie settings, and session service. |
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
| Forms | `src/Project/Factory/Form/`, `src/Project/Controller/Stuff/` | `form` |
| API request | `src/Project/Middleware/API/ApiAuthenticationMiddleware.php`, `src/Project/Controller/API/` | `jwt`, `jsonapi`, `jsonapi-application` |
| Stuff persistence | `src/Project/Factory/Container/StuffLocalDependencyContainerFactory.php` | `app-stuff-common`, `database`, `data` |
| CLI | `bin/SandboxTest.php`, `src/Project/Factory/Command/` | `command`, `application-default` |
