# PHP: Application architecture

## Main request path

### Project directory map

| Path | Responsibility |
| --- | --- |
| `src/Project/Command/` | CLI command implementations. |
| `src/Project/Container/` | Application and module-local container implementations. |
| `src/Project/Contract/` | App-specific interfaces, including module controller markers and container/form-factory contracts. |
| `src/Project/Controller/` | Request-to-response orchestration. Controllers use application and local dependencies, then create responses and views. |
| `src/Project/Factory/` | Constructs applications, commands, containers, forms, request handlers, and middleware. |
| `src/Project/Instantiator/` | Selects and configures module-specific controller instantiators. |
| `src/Project/Middleware/` | Project-specific request processing, currently session and API authentication. |
| `src/Project/RequestHandler/` | Module request handlers, including renderer capabilities and special route behavior. |
| `src/Project/Service/` | Project-specific services and validators. |
| `src/Project/View/` | Typed view data objects passed to renderers. |
| `config/{Module}/Routes.php` | Route-key-to-controller maps loaded at runtime. |
| `resources/templates/vanilla/` | HTML page templates and module-specific templates. |
| `public/` and `bin/` | Web and CLI entry points. |
| `tests/` | HTTP request examples; PHPUnit is configured, but no unit tests are currently present. |

### Web bootstrap

`public/index.php` performs the shared web initialization in this order:

1. Capture a high-resolution start time with `hrtime()`.
2. Resolve the project root and include Composer's autoloader.
3. Create a `NullLogger`, then start the lap timer and create the `ApplicationDependencyContainer`.
4. Resolve the application logger and load `config/.env.ini` through `ConfigurationFileProcessor`.
5. Register the uncaught exception handler and record initialization timing.
6. Create the server application factories, create the PSR-7 server request from PHP's server data, bootstrap the default application, and run it.

Initialization is wrapped in a `Throwable` catch. If setup fails before normal application error handling is ready, the script logs an emergency message when possible, prints a generic initialization message, and exits with status 1. After initialization, exceptions are handled by the application middleware or registered uncaught handler.

`public/.htaccess` sends paths that are not existing files or directories to `index.php`. Apache is the configured DDEV web server.

### Middleware order

`RequestHandlerFactory::createRequestHandler()` adds these middlewares to `StackHandler` in the listed order. `StackHandler` consumes the stack from the first added middleware to the last:

1. **Exception handler 1** wraps the rest of the pipeline and can render exceptions thrown during content negotiation or routing.
2. **View renderer setting** reads `Accept` and sets the requested renderer interface on the request. A missing header is treated as `*/*`.
3. **Route middleware** maps a recognized URL prefix to numbered route-part attributes.
4. **Session authentication** only acts when route part 1 is `stuff`; other modules pass straight through.
5. **Request logger** logs requests that continue to this point.
6. **API authentication** only acts when route part 1 is `api`.
7. **Exception handler 2** wraps resource dispatch and controller execution so application exceptions can use the normal response path.
8. **Resource middleware** selects the dynamic request handler for `api`, `sandbox`, or `stuff`.
9. The selected module handler resolves its route configuration, chooses a renderer, instantiates the controller, and calls `handle()`.

When no middleware handles a request, `StackHandler` calls its fallback `ExceptionRequestHandler`, configured with `NotFoundController`. A recognized module prefix with an unconfigured route reaches its module handler and currently throws `UnexpectedValueException`; that is sent through `ErrorController`, rather than the fallback 404 controller.

The stack is consumed as it handles a request. The application runner creates a request handler for the server application run, which handles that request once.

### Route parts and route configuration

`RouteMiddleware` uses a base path of `/`, default route `sandbox/test`, and the handled prefixes `api`, `sandbox`, and `stuff`. It strips the base path and surrounding slashes, splits the rest on `/`, and stores the parts as request attributes named by the HTTP request component's route-part template.

| Request | Route part 1 | Route part 2 | Route part 3 | Route lookup |
| --- | --- | --- | --- | --- |
| `/` | `sandbox` | `test` | — | `config/Sandbox/Routes.php` key `test` |
| `/sandbox/test/hello` | `sandbox` | `test` | `hello` | key `test`; the controller reads part 3 |
| `/stuff/items/42` | `stuff` | `items` | `42` | `config/Stuff/Routes.php` key `items` |
| `/api/v1/version` | `api` | `v1` | `version` | API handler combines parts 2 and 3 as `v1/version` |

`ResourceMiddlewareFactory` loads each `config/{Module}/Routes.php` file and builds `RouteConfiguration` objects from the string-to-controller map. Standard dynamic handlers use part 2 as the key. The API handler uses the API version as part 2 and endpoint name as part 3, with an optional fourth part.

Route parts are stripped of tags and passed through `htmlspecialchars()` by the shared route middleware. This is route normalization, not a substitute for validating a value for the operation that consumes it or escaping output for its final context.

### Controller creation and dependencies

Every application controller implements `WebServCo\Controller\Contract\ControllerInterface` through a module marker interface in `Project\Contract\Controller`. The module marker is the dispatch key for controller creation:

1. `ControllerInstantiator` checks the configured class exists and implements the controller contract.
2. `SpecificModuleControllerInstantiator` checks its ordered interface-to-instantiator map.
3. The selected module instantiator creates that module's local dependency container.
4. `AbstractModuleControllerInstantiator` validates the controller constructor types and uses reflection to pass the application container, local container, and view-services container.

The most specific marker interface must appear before any broader marker in `SpecificModuleControllerInstantiator::getAvailableModuleControllerInstantiators()`.

There are three dependency scopes:

- **Application container**: shared configuration, data-extraction, HTTP factories and services, logger, session, and lap timer.
- **Module-local container**: services needed only in a module. The Sandbox and Error modules use an empty container; API uses the JSON:API local container; Stuff adds its form factories and storage container.
- **View-services container**: the selected renderer and `ViewContainerFactory` for the current controller.

Container implementations lazily create and cache their contained objects. Module factories are the place to add module-specific dependencies; controller constructors should receive their existing three container arguments from the instantiator.

### Content negotiation and rendering

The middleware translates supported `Accept` values into renderer interfaces:

- `text/html` selects HTML where the module provides it.
- `application/json` selects JSON for Sandbox and Stuff.
- `application/vnd.api+json` selects JSON:API for the API module.
- `*/*` means any renderer; the resolver picks the first renderer in the module's renderer list.

The module handlers declare supported renderers in `getAvailableViewRenderers()`. The API list puts JSON:API first and HTML second; Sandbox and Stuff list HTML first and JSON second. Unsupported explicit media types throw and are handled by exception middleware. The exception request handler has its own HTML/JSON/JSON:API renderer set and falls back to JSON if no requested type can be resolved.

Controllers create a view object, wrap it in a `ViewContainer` with a template name such as `stuff/item`, and pass the container to `createResponse()`. The view container gets its `TemplateService` from `Project\Controller\AbstractController`, which points at `resources/templates/vanilla` and appends `.php`.

For HTML responses, `AbstractDefaultControllerBase` renders the page view first, puts that HTML string in `MainView::data`, then renders the module's main template. API, Sandbox, Stuff, and Error abstract controllers select their own main template. For non-HTML renderers, the page container is rendered directly and no HTML main view is wrapped around it.

Each template receives its typed view as `$view`. Templates assert the expected view type, then output that view's values. `AbstractView::escape()` performs HTML escaping for a nullable string. Use it for dynamic text and attribute values; filtering input or sanitizing a route does not make a value safe for HTML.

### Response creation

`AbstractDefaultController::createResponse()` creates the response through the application response factory, renders a body when a view is present, and sets `Content-Type` from the selected renderer and `Content-Length` from the stream size. `createRedirectResponse()` checks that the status is a redirect status before adding the `Location` header. The project-level `createLocalRedirectResponse()` uses configured `BASE_URL` and defaults to `303 See Other`.

## API authentication and response path

`ApiAuthenticationMiddleware` runs for the `api` prefix. It expects an `Authorization: Bearer <JWT>` header, decodes using HS256 and `JWT_SECRET`, requires scalar `iss` and `sub` claims, and accepts the token only when `iss` equals configured `API_KEY`. It writes `sub` to the request's `userId` attribute. API controllers read that attribute through the strict data-extraction service.

`ApiRequestHandler` advertises JSON:API and HTML renderers. `APIController` creates a JSON:API handler that accepts GET, processes the request, and builds the example `ItemView`; its metadata includes the route and configured API version. Invalid request data is converted into JSON:API error objects by `AbstractAPIController`. Both `v1/about` and `v1/version` currently map to `APIController`; endpoint-specific business logic is not implemented.

## Session authentication path

`AuthenticationMiddleware` acts only under `/stuff`. It starts a session in web SAPIs, permits `/stuff/authenticate` through while unauthenticated, and otherwise stores a next-location value and returns a 303 redirect. An authenticated request receives `isAuthenticated` and `userId` attributes.

`AuthenticationController` builds a password form using the configured password. A valid submission stores `isAuthenticated` and a generated `userId` in the session, clears the saved next location, and redirects back to the requested page (or `/stuff/items`). The logout controller destroys the session and redirects to authentication.

This is a small demonstration login: it uses one configured shared password and derives its example user id from that password. It is not a full user-account or production authentication design.

## Error paths

- **Unknown top-level path**: no route prefix claims it, so the stack fallback invokes `NotFoundController` and returns 404.
- **Unknown route inside a known module**: the dynamic handler throws, and exception middleware invokes `ErrorController`.
- **Application exception during dispatch**: exception middleware logs through the exception handler, adds the throwable to the request, then calls the configured error controller.
- **Exception before middleware is built or an exception while rendering an error**: the registered uncaught handler logs and emits a generic message; in a web SAPI it sets status 500.

`ErrorController` reveals exception code and message only in development and testing. In staging and production it uses a generic message and a 500 display code. It accepts an exception's numeric HTTP status only if the status code is recognized; otherwise it responds with 500.
