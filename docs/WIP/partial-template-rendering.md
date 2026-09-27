# Partial template rendering

## Status

This document is a proposal for a future implementation. It describes the
application-level design; it does not change the current rendering code.

The proposal is intended as a reference for both humans and AI systems working
on the sandbox application.

## Purpose

The application currently renders HTML in two stages:

1. A controller creates a content `ViewContainer` containing a typed view and
   a page template such as `stuff/items`.
2. The controller renders that content view to an HTML string, places the
   string in `MainView::$data`, and renders a main template such as
   `main/main.stuff.pico`.

The proposed partial implementation keeps this structure and adds more
rendered view containers alongside the content container. The main view then
receives the rendered partial output as typed string properties.

```text
content ViewContainer  ─┐
                        ├─ render with the selected HTML renderer
partial ViewContainers  ─┘
                        ↓
project MainView(data + rendered partials)
                        ↓
main ViewContainer
                        ↓
HTML response
```

Partials therefore follow the same controller/view/rendering path as the
current main/content relationship. Main templates only output view properties;
they do not locate or include partial files themselves.

## Current rendering boundaries

The relevant application code is:

- `src/Project/Controller/AbstractController.php`: selects the template group
  through `createTemplateService()`.
- `vendor/webservco/controller/src/WebServCo/Controller/Service/AbstractDefaultControllerBase.php`:
  assigns the template service, renders HTML content, and creates the main
  view container.
- `vendor/webservco/controller/src/WebServCo/Controller/Service/AbstractDefaultController.php`:
  creates the current `WebServCo\View\View\MainView` and stores rendered page
  content in its `data` property.
- `vendor/webservco/view/src/WebServCo/View/Service/AbstractTemplateViewRenderer.php`:
  validates a template path and renders a `ViewContainer` with output buffering.
- `vendor/webservco/view/src/WebServCo/View/Service/ViewContainer.php`:
  combines a view, template name, template base path, and file suffix.

`TemplateService` describes the template root and filename suffix. It is not a
partial lookup service. Partial rendering should therefore be added at the
project controller boundary rather than added to `createTemplateService()`.

Non-HTML rendering must remain unchanged. The base controller currently skips
main-template creation when the selected renderer is not the HTML renderer;
partial rendering must only occur inside the HTML path.

## Proposed application types

### `MainTemplateConfiguration`

Add a readonly application DTO, for example:

`src/Project/DataTransfer/View/MainTemplateConfiguration.php`

It should contain the main template name and optional extensionless partial
template names:

```php
final readonly class MainTemplateConfiguration
{
    public function __construct(
        public string $templateName,
        public ?string $headTemplateName = null,
        public ?string $headerTemplateName = null,
        public ?string $footerTemplateName = null,
        public ?string $scriptsTemplateName = null,
    ) {
    }
}
```

The names must use the same format as existing `ViewContainer` names. The
`.php` suffix is supplied by `TemplateService`.

### Application `MainView`

Add an application-level layout view, for example:

`src/Project/View/MainView.php`

It should extend `AbstractView`, implement `ViewInterface`, and contain typed
readonly properties:

```php
final class MainView extends AbstractView implements ViewInterface
{
    public function __construct(
        public readonly CommonView $commonView,
        public readonly string $head,
        public readonly string $header,
        public readonly string $data,
        public readonly string $footer,
        public readonly string $scripts,
    ) {
    }
}
```

The `data` property preserves the current page-content convention. The other
properties contain already-rendered HTML from partial templates and must be
output as HTML by the main template, just like the existing `data` property.

This application view is only used for the HTML main-template path. JSON and
other non-HTML renderers continue receiving the original page view directly.

## Proposed controller changes

Extend `Project\Controller\AbstractController` with a project-level helper
similar to the existing vendor `createMainViewContainerWithTemplate()`:

```php
protected function createMainViewContainerWithConfiguration(
    ServerRequestInterface $request,
    MainTemplateConfiguration $configuration,
    ViewContainerInterface $viewContainer,
): ViewContainerInterface
```

The method should:

1. Render the supplied content `ViewContainer` through the selected renderer.
2. Create the shared `CommonView` for the request.
3. Render each configured partial through a new helper such as:

   ```php
   protected function renderTemplateView(
       ViewInterface $view,
       string $templateName,
   ): string
   ```

4. Have that helper create a normal `ViewContainer` through the existing
   `ViewContainerFactory`.
5. Assign a `TemplateService` created from the configured project path.
6. Render the partial container through the existing selected renderer.
7. Create the application `MainView` with the content and partial strings.
8. Create and return the final main `ViewContainer` using the configured main
   template name.

Optional partial template names should produce an empty string. The helper
must use explicit local variables for the factory, template service, and view
container, following the project guidance against compressed factory calls.

The existing `createTemplateService()` method should continue selecting
`resources/templates/vanilla` and `.php`. It should not contain partial
rendering logic.

## Module configuration

Replace module calls to the vendor helper with the project helper and explicit
configuration objects.

Expected configurations are:

| Module or page | Main template | Partial templates |
| --- | --- | --- |
| Stuff | `main/main.stuff.pico` | shared head, Stuff header, Stuff footer |
| Authentication | `main/main.stuff.notauthenticated.pico` | shared head |
| Error | `main/main.error.pico` | shared head, Error header, Error footer |
| Sandbox | `main/main.sandbox.default` | shared head |
| API HTML fallback | `main/main.api.default` | shared head |

The module controllers should pass these names explicitly. This avoids
deriving partial names from a main-template string or from request data.

## Template organization

Store partials inside the selected template group:

```text
resources/templates/vanilla/
├── main/
├── partials/
│   ├── head.php
│   ├── stuff/
│   │   ├── header.php
│   │   └── footer.php
│   └── error/
│       ├── header.php
│       └── footer.php
├── sandbox/
├── stuff/
└── error/
```

Each partial is an ordinary PHP template rendered by `HTMLRenderer`. It should
begin with `declare(strict_types=1);` and assert the expected view type when it
uses view data.

The initial layout partials can receive the existing `CommonView`, which
provides `baseUrl` and `currentUrl`. A future partial requiring additional data
should receive a dedicated readonly view class. Do not add request parsing,
container access, storage calls, or business logic to partials.

Main templates should import `Project\View\MainView` and output its partial
properties in the relevant document positions:

```php
<?=$view->head?>
<?=$view->header?>
<?=$view->data?>
<?=$view->footer?>
<?=$view->scripts?>
```

The rendered properties must not be passed through `escape()`, because doing so
would escape their HTML markup. Values generated inside partials still require
context-appropriate escaping through their supplied view.

## Standards and design constraints

- Do not modify the vendor WebServCo view or controller packages for the first
  implementation.
- Do not add a renderer or service locator to templates.
- Do not use dynamic template paths based on request or user input.
- Keep controllers responsible for composition and views responsible for
  typed display data.
- Keep construction of application DTOs and views explicit and typed.
- Use `#[Override]` when overriding existing methods.
- Import classes, functions, and constants according to the configured coding
  standard.
- Preserve the existing HTML escaping rules for dynamic values.
- Keep JSON and other non-HTML response behavior unchanged.

The repeated item markup in `stuff/items.php` and `stuff/search_item.php` should
be handled separately. Since those templates currently use different view
types, a shared item component would need its own typed view or DTO rather than
an untyped collection of variables.

## Implementation sequence

1. Add `MainTemplateConfiguration` and the application `MainView`.
2. Add the controller rendering helpers in `AbstractController`.
3. Add the common and module-specific partial templates.
4. Update module abstract controllers to pass explicit configurations.
5. Update all main templates to assert the application `MainView` and output
   the configured partial properties.
6. Update the architecture and coding-standards documentation after the
   implementation is verified.

## Acceptance checks

The implementation is complete when:

- HTML page content still renders inside the configured main template.
- Head, header, footer, and script partials appear in the intended positions.
- `/stuff/authenticate`, `/stuff/items`, Sandbox pages, and error responses use
  their intended partial sets.
- Missing partial templates fail through the existing template-rendering error
  path.
- JSON responses do not attempt to render HTML partials.
- Existing escaping behavior remains intact.

Use the repository checks after implementation:

```sh
ddev composer check:lint
ddev composer check:phpcs
```

The existing HTTP examples should also be used for HTML, JSON, authentication,
and error-path smoke checks.
