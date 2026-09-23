# PHP: Coding standards and development

## PHP conventions in application code

- Require PHP `^8.4`; DDEV currently runs PHP 8.5.
- Begin PHP files with `declare(strict_types=1);`.
- Use PSR-4 namespaces that follow the directory: `Project\Controller\Stuff` lives under `src/Project/Controller/Stuff/`.
- Give application classes and methods native parameter and return types. Use `#[Override]` on implementations that override a parent or interface method.
- Prefer constructor property promotion for injected dependencies, typed properties, and `final` concrete classes. Use abstract classes where a shared base behavior is intentional.
- Define narrow contracts for module controllers, module containers, and module form factories. Put app-specific contracts in `src/Project/Contract/`.
- Use readonly, typed view/DTO properties for data passed between layers. Views should carry display data, not perform database or request parsing work.
- Import global functions and constants explicitly when they are used in namespaced classes. Use PHPDoc generics for arrays and generators where native types cannot express the element type.
- Keep constructors and service creation in factories and containers. Controllers should orchestrate the request and call services through their injected dependencies.
- Use descriptive names for route parts, form fields, configuration keys, and container accessors. Preserve current casing of route keys and template names.
- Add local comments where an example has unusual behavior or a static-analysis suppression. Keep suppressions narrow and tied to the relevant rule.

These are the patterns visible in the code and enforced in part by the configured tools; the concrete PHP CodeSniffer ruleset remains the final style authority.

## Configured checks

Composer scripts are the executable reference. In a configured DDEV environment, run:

| Command | What it checks |
| --- | --- |
| `ddev composer check:lint` | PHP syntax across `bin`, `config`, `public`, `resources`, `src`, and `tests`. |
| `ddev composer check:phpcs` | The project's PSR-12, PHPCompatibility, and Slevomat ruleset. |
| `ddev composer check:phpstan` | PHPStan at maximum level, with strict and deprecation rules. |
| `ddev composer check:phpmd` | Clean-code, size, controversial, design, naming, and unused-code rules. |
| `ddev composer check:psalm` | Psalm using the shared WebServCo config. |
| `ddev composer check:phan` | Phan using the shared WebServCo config. |
| `ddev composer check` | Runs lint, PHPCS, PHPStan, PHPMD, Psalm, and Phan in sequence. |
| `ddev composer check:skeleton` | PDS skeleton validation; separate from `check`. |
| `ddev composer test` | PHPUnit using the shared PHPUnit 10 configuration. |

`ddev composer fix:phpcs` runs PHP Code Beautifier and Fixer with the same project ruleset. Review its changes rather than assuming an automatic fix is semantically correct.

## Where the rules come from

- `.phpcs/php-coding-standard.xml` includes `vendor/webservco/coding-standards/phpcs/ruleset-psr-php83-slevomat.xml` and maps the project, tests, and WebServCo roots.
- The shared PHPCS ruleset combines PSR-12, PHPCompatibility, and Slevomat. It tests compatibility against PHP 8.3 and documents selected exceptions for PHP features, naming, spacing, and readability.
- `vendor/webservco/coding-standards/phpstan/phpstan.neon` sets maximum analysis and includes PHPStan deprecation, strict, and PHPUnit rules. Composer also passes `--level=max`.
- The shared Psalm config analyses project PHP directories, excludes `vendor/` and `var/`, checks array offsets, and enables unused variable/parameter checks.
- Phan parses project directories and `vendor/` so dependency symbols are available, while excluding `vendor/` from analysis. Its config enables strict checking and several correctness plugins.
- The PHPMD ruleset includes all standard rule groups with a few project-wide naming adjustments.
- PHPUnit's shared config targets PHPUnit 10, `tests/Unit`, and the app's `src` for coverage.

The PHPCS ruleset also enforces file/type-name matching using this repository's PSR-4 roots, alphabetical attribute ordering, and a 25-line function length target. It requires imported references for used classes, functions, and constants. The Slevomat rules are customized to allow constructor property promotion, named arguments, and PHP 8 trailing commas, while keeping selected PSR-12 spacing rules authoritative. Longer methods or exceptional cases use narrow inline suppressions.

The project requirement is PHP `^8.4`, while the included PHPCS ruleset is named PHP83 and sets PHPCompatibility's `testVersion` to `8.3`. Treat that as the current configured baseline when diagnosing checks; do not infer the PHP runtime requirement from the ruleset name.

## Tests and HTTP examples

The repository currently contains `tests/HTTP/HTTPTests.http`, with cURL and REST Client examples for content negotiation, exception handling, 404s, Sandbox routes, and the API. There are no PHPUnit test files under `tests/Unit/` at present, even though the PHPUnit script and shared configuration are set up for them.

The HTTP collection assumes the local DDEV host and current routes. Adapt the URL and request credentials/configuration to the environment before using the API examples.

## Documentation conventions

- Documentation source is under `docs/`; MkDocs reads the Markdown tree and `mkdocs.yml` sets the site name.
- Match the existing heading hierarchy and use Markdown links between related pages.
- Keep behavior notes beside the module or workflow they describe. If implementation behavior changes, update the corresponding docs and HTTP examples in the same change.
- The README documents `mkdocs serve` for local preview and `mkdocs gh-deploy` for publishing. The generated `site/` directory is ignored by Git.
- `docs/Development/PHP/Application/application_workflow.md` is explicitly partial and has TODO steps. Use the current source and this WIP guide for the complete existing workflow.

## Current examples that need care

These are facts about the sandbox examples, not general recommendations for production applications:

- **HTML output:** several Stuff templates print item names, descriptions, titles, and form error text directly. Other templates use `AbstractView::escape()`. Escape dynamic text and attribute data at the output context; `strip_tags()` during form processing is not an HTML output-escaping strategy.
- **Deletion:** `item-delete/{id}` is a link that performs a state change on a GET request and has no confirmation or CSRF token. Use an appropriate state-changing method and CSRF protection in a real application.
- **Authentication:** the Stuff module compares against one configured password and derives a user id from it. It is a demonstration path, not a user store or a production password design.
- **API examples:** the two configured endpoint names share one controller and sample resource response. Implement route-specific behavior and authorization before using the pattern as an actual API.
- **Routing errors:** an unknown top-level prefix reaches the 404 fallback, while an unknown route inside a recognized module currently becomes an exception response.
- **Persistence setup:** the item storage code assumes the shared package's `stuff_item` table. No schema or migration is present here.
- **Dependency resolution:** `.gitignore` excludes `composer.lock`, so Composer constraints in `composer.json` do not pin exact resolved versions in Git. For a deployed application that needs reproducible installs, commit and maintain an appropriate lock file.
