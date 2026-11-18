# PHP: Feature workflows

## Add a page to an existing module

For an ordinary page in an existing module, follow this sequence:

1. Add a route key and controller class to the module's `config/{Module}/Routes.php`.
2. Create the controller under `src/Project/Controller/{Module}/`. Implement the module marker controller interface, commonly by extending that module's abstract controller.
3. Create a view under `src/Project/View/{Module}/`. Keep the view as a typed data object; the existing views extend `AbstractView` (the Sandbox ones through `AbstractLocalizedView`, see [Translate a page](#translate-a-page)) and expose constructor-promoted `public readonly` properties.
4. In the controller's `handle()` method, collect validated inputs and call services. Create a view container with the view factory and a template name relative to `resources/templates/vanilla`.
5. Add the matching PHP template. Assert the expected view class and escape dynamic HTML output.
6. If the page needs a service not already available, add it through the module-local dependency-container interface, implementation, and factory.

The existing Sandbox route is the smallest example: `config/Sandbox/Routes.php` maps `test` to `TestController`; the controller reads route part 3, creates `TestView` in the language of the request, selects `sandbox/test`, and uses the Sandbox main layout.

For a controller response, the usual shape is:

```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $viewContainer = $this->viewServicesContainer
        ->getViewContainerFactory()
        ->createViewContainerFromView(
            new ExampleView($someValue),
            'module/example',
        );

    return $this->createResponse($request, $viewContainer);
}
```

The inherited controller base provides the application, local, and view-services containers, `createResponse()`, `createCommonView()`, and the HTML page-layout path. Use the module's abstract controller when it already chooses the correct main layout.

## Translate a page

The Sandbox module's pages are translated with `webservco/i18n` ([Languages and translations](components.md#languages-and-translations)). For a page of that module:

1. Add its texts to the English catalog, `resources/i18n/en/{catalog}.php` (`sandbox` for this module), as `name => message`. English is the reference: it has every key. Messages are plain text in [ICU MessageFormat](https://unicode-org.github.io/icu/userguide/format_parse/messages/): `{count, plural, one {# item} other {# items}}`.
2. Add the same names to `resources/i18n/de/{catalog}.php`. A plural has exactly the plural categories of its language (English and German: `one` and `other`).
3. Let the view extend `AbstractLocalizedView`, and pass `$this->getLocalizationContainer($request)` as its first constructor argument.
4. In the template, print `$view->text('sandbox.test.date')`, `$view->text('sandbox.test.items', ['count' => $count])`, `$view->date($dateTime)` or `$view->number($number, 2)`. They return escaped text. Write each key in full: the checks find keys in the code by their form.
5. Run the tests (`ddev xdebug on && ddev composer test; ddev xdebug off`): `CatalogTest` reports a key English lacks, a key only one language has, a wrong plural, a US spelling, and the other catalog problems.
6. Look at the page in both languages: the language switch links to `?lang=de` and `?lang=en`.

`getLocalizationContainer()` is in `Project\Controller\AbstractController`, so every module has it. A page of another module needs its view to extend `AbstractLocalizedView`, and its layout a localized view if the layout has texts: the Stuff module's `Project\View\Stuff\MainView` is localized for its flash messages ([Show a result message](#show-a-result-message)).

A new language is a case of `Project\Enum\I18n\Language` (code, ICU locale, native name) and a catalog directory `resources/i18n/{code}/` with every catalog and name of English.

## Add a new top-level module

Adding a module requires wiring each dispatch boundary:

1. Add its route prefix to the handled paths in `RequestHandlerFactory`.
2. Add a module request handler to `ResourceMiddlewareFactory` and include it in the resource-handler map. The handler declares its renderer interface-to-class map and uses the right route-part convention.
3. Add `config/{Module}/Routes.php`.
4. Add a marker interface under `src/Project/Contract/Controller/` and a module controller instantiator under `src/Project/Instantiator/Controller/`.
5. Add the marker-to-instantiator pair to `SpecificModuleControllerInstantiator`, keeping the required order.
6. Add a module-local dependency-container contract, implementation, and factory only when the module needs its own services.
7. Add an abstract module controller when pages share layout or module-specific request helpers.
8. Add views, templates, and tests or HTTP examples as appropriate.

A module is more than a route-file directory: route-prefix dispatch and controller construction are both explicit.

## Handle a form

### Factory

Create a form factory that implements `FormFactoryInterface` (or a module-specific child interface) and returns an `HtmlPostForm`. Define each `FormField` with an id, required flag, submitted name, label/placeholder, field validators, and optional initial value.

The current `HtmlPostForm` reads submitted data using each field's id, while templates write the field's name attribute. The examples use the same string for both; preserve that mapping or provide a form implementation that supports a different mapping.

`Project\Factory\Form\AbstractFormFactory` provides the app's shared form filters and validators:

- Form-level filters: trim, then strip HTML tags.
- Form-level validator: required-field validation.
- Helpers for minimum and maximum character length, with errors carrying a 400 code.

The Stuff examples add field-specific validators. `ItemFormFactory::setItem()` supplies initial values for edit mode. The form-factory container provides a single lazily created factory per local container.

### Controller lifecycle

1. Get the module form factory and create the form.
2. Call `handleRequest($request)`.
3. On a non-POST request, `HtmlPostForm` records a method error and is not sent; the page can render with 200.
4. On a POST, it reads scalar values from the parsed body, filters values, then runs general and field-level validators.
5. If `isSent()` and `isValid()` are both true, convert validated values to the operation's DTO and call the service/storage layer.
6. Redirect after successful mutation, usually with a flash message ([Show a result message](#show-a-result-message)). Otherwise render the form with the form's response status.

`AuthenticationController`, `ItemController`, and `SearchItemController` show the pattern. Keep input interpretation in the request/form path and pass a typed DTO such as `WebServCo\Stuff\DataTransfer\Item\Item` into storage.

The form filters are input processing, not output escaping. Escape values again when inserting them into HTML or attributes.

### CSRF token

`CsrfMiddleware` refuses every POST without the session's token, in every module except `api` ([Session, CSRF token and flash messages](components.md#session-csrf-token-and-flash-messages)). So every form carries it in a hidden field:

1. The controller gives the view the token: `$this->getLocalDependencyContainer()->getCsrfTokenService()->getToken()`. It starts the session and creates the token when needed. A view property such as `public readonly string $csrfToken` holds it.
2. The template writes the field first in the form:

    ```php
    <input type="hidden" name="<?=CsrfTokenServiceInterface::FIELD_NAME?>" value="<?=$view->csrfToken?>">
    ```

3. A request sent with JavaScript can send the token in the header `X-CSRF-Token` (`CsrfTokenServiceInterface::HEADER_NAME`) instead.

The form classes ignore the field. A form of a module without the services in its local container gets them the way the Stuff container does: `CsrfTokenService` and `FlashMessageService` on the store of `SessionStoreFactory`. `AuthenticationController` (`stuff/authentication`), `ItemController` (`stuff/item`), `ItemDeleteController` (`stuff/item_delete`), and the search form and logout button of the Stuff layout show the field.

## Show a result message

A flash message tells the user the result of an action on the next page, once ([Session, CSRF token and flash messages](components.md#session-csrf-token-and-flash-messages)). In the Stuff module:

1. Add the text to `resources/i18n/en/stuff.php` and `resources/i18n/de/stuff.php` (`flash.{name}`), as for a translated page ([Translate a page](#translate-a-page)).
2. In the controller, right before the redirect:

    ```php
    $this->getLocalDependencyContainer()->getFlashMessageService()->add(
        FlashMessageType::Success,
        'stuff.flash.logged_in',
    );
    ```

    Write the key in full, so that the catalog check finds it. The types are `Success`, `Info`, `Warning` and `Danger`. Parameters of the message go into a third argument, `['name' => $value]`. The same message waiting twice is stored once.
3. Nothing else: the layout (`AbstractStuffController::createStuffMainViewContainer()`) takes the waiting messages after the page is rendered and shows them in the language of the request. A JSON response or a redirect leaves them waiting.

`AuthenticationController` (login, `Success`), `LogoutController` (logout, `Info`; after `destroy()`, the message starts a new session) and `ItemDeleteController` (`Success`, with the item's name as a parameter) are the examples. Another module needs the same layout part: a layout view with the messages, filled by its abstract controller.

## Confirm a destructive action

Every state change is a POST with the token, so a link can't change anything. A destructive action asks first, with one URL for both steps:

1. **GET**: a confirmation page with the question and a POST form to the same URL (`action` is the current URL), with only the token field, a submit button and a cancel link.
2. **POST**: the action, a flash message ([Show a result message](#show-a-result-message)), and a redirect (303). The CSRF middleware has checked the token before the controller runs.

The controller tells them apart by the method (`RequestMethodInterface::METHOD_POST`). A link to the action points at the GET page, so it works without JavaScript. The example is the item deletion: the item page links to `item-delete/{id}`; `ItemDeleteController` loads the item (it throws when the user has no such item), shows `stuff/item_delete` or deletes it and redirects to the list it was in.

An action that destroys nothing, such as logout, needs no confirmation page: a POST form with the token (the logout button of the Stuff layout), and a GET of its URL only redirects.

## Add or change a Stuff storage operation

The Stuff controllers depend on `StuffLocalServiceContainerInterface`, whose implementation exposes a storage container. The project-specific local container composes the shared library's `StuffStorageContainer` with the application's data-extraction and PDO containers.

The shared `webservco/app-stuff-common` package separates storage responsibilities:

- `ItemStorage` creates, retrieves, updates, and deletes an item.
- `ItemEntityStorage` retrieves and iterates records with ids, parent ids, and item counts.
- `SearchItemEntityStorage` iterates matching records.
- `Item` is the data-transfer object; `ItemEntity` adds persistence identity and parent/count data.

The current SQL uses prepared statements and scopes item lookup and mutation by both item id and user id. Keep that user scope on every operation that addresses a private record. `ItemController` demonstrates create/update; `ItemsController` demonstrates parent and ancestor loading; `SearchItemController` demonstrates search; `ItemDeleteController` demonstrates deletion. The SQL and expected table live in the shared package. There is no schema or migration in this repository, so a new deployment must supply its own database setup.

For a new application-specific dependency, extend the local-container contract, add a lazy getter to its implementation, and construct it in the module local-container factory. Keep construction in factories/containers rather than controllers.

## Add an API route

1. Add an API route key such as `v1/example` in `config/API/Routes.php`.
2. Point it at a controller implementing `APIControllerInterface`, or add a more specific controller marker and update the ordered module-instantiator map.
3. `ApiRequestHandler` forms the lookup key from API version (route part 2), endpoint (part 3), and optional part 4.
4. Add request validation and response construction in the controller. Use the JSON:API handler/service and DTOs when the endpoint follows JSON:API.
5. Ensure `application/vnd.api+json` is accepted by the client. `application/json` is not the JSON:API media type.
6. Add an HTTP example under `tests/HTTP/HTTPTests.http`.

The existing API controller is an example, not a finished resource API: both configured paths use it, it accepts GET, and it returns the same sample resource shape. The middleware's identity is the signed JWT subject; the controller should still authorize access to any requested resource.

## Add middleware

Implement `Psr\Http\Server\MiddlewareInterface`, inject the services it needs, and return either a response or `$handler->handle($request)` with updated immutable request attributes. Register it in `RequestHandlerFactory` at the point that establishes the needed ordering:

- before route middleware if it must run without route attributes;
- after route middleware if it is prefix-specific;
- outside an exception middleware if its own failures must be handled by that layer;
- inside an exception middleware if its failures should be converted through the configured error controller.

The project has separate session and API authentication middleware because they have different route scopes and credentials.

## Add a CLI command

1. Add the command under `src/Project/Command/` and implement `CommandRunnerInterface`, usually by extending `Project\Command\AbstractCommand`.
2. Add a `CommandFactoryInterface` implementation under `src/Project/Factory/Command/`; inject shared dependencies from the application container.
3. Add a small executable wrapper in `bin/` if the command should be launched directly.
4. In the runner script, use the same startup sequence as `bin/SandboxTest.php`: autoload, `DefaultApplicationInitializer` (timer, application dependency container, logger, config file, uncaught handler), command factory, and `DefaultCommandApplicationFactory`.
5. Bootstrap the command application, and end the script with `exit($application->run() ? 0 : 1);` so the command's result becomes the exit code.

`bin/sandbox-test` is deliberately a thin PHP executable wrapper for `bin/SandboxTest.php`. The command itself writes through the command output service and records lap-timer events.

## Local development references

- Start the environment with `ddev start`.
- Install Composer dependencies with `ddev composer install`.
- Run the CLI example with `ddev exec bin/sandbox-test`.
- The HTTP request collection is `tests/HTTP/HTTPTests.http`; it can be used with the VS Code REST Client extension or by running the included cURL examples.
- The project's Composer scripts are listed in [Coding standards and development](coding-standards.md).
