# PHP: Sandbox: building applications

## Purpose

This guide explains how the sandbox application is assembled and how its WebServCo components are used. It is intended as a practical reference for people and AI systems that need to build another application using the same architecture.

The guide describes the code and dependency implementations currently installed in this repository. Older application notes include TODOs and partial sketches; where those differ from the running code, follow the code and the pages here.

## Read the guide

- [Application architecture](application-architecture.md): bootstrap, middleware order, routing, controller construction, views, and errors.
- [Feature workflows](feature-workflows.md): add a page or module, handle forms and storage, add API routes, and create commands.
- [Component reference](components.md): the responsibilities of the WebServCo packages and where this app uses them.
- [Coding standards and development](coding-standards.md): PHP patterns, configured checks, documentation, and examples that need extra care.

Existing reference pages:

- [Sandbox modules and example endpoints](../Development/PHP/Sandbox/index.md)
- [Application setup notes](../Development/PHP/Application/application_setup.md)
- [Application structure notes](../Development/PHP/Application/application_structure.md)
- [Application workflow notes](../Development/PHP/Application/application_workflow.md)
- [Error handling notes](../Development/PHP/ErrorHandling.md)

## Application profile

- Composer package: `webservco/sandbox`
- PHP requirement: `^8.4`
- DDEV configuration: PHP 8.5, Apache, MariaDB 11.8; the web root is `public/`
- PHP autoloading: `Project\` maps to `src/Project/`; the Composer map for `WebServCo\` points to `src/WebServCo/`, which currently contains no application classes.
- HTTP entry point: `public/index.php`, reached through `public/.htaccess`
- CLI example: `bin/sandbox-test`, which loads `bin/SandboxTest.php`
- Templates: native PHP under `resources/templates/vanilla/`

The app uses explicit factories and containers around PSR interfaces. Controller constructors are created by a reflection-based WebServCo controller instantiator, but services are not discovered through general-purpose autowiring.

## Modules and routes

| Prefix | Examples | Purpose |
| --- | --- | --- |
| `/api` | `/api/v1/about`, `/api/v1/version` | JWT-protected JSON:API example. Both configured routes currently use the same example controller and response shape. |
| `/sandbox` | `/sandbox/test`, `/sandbox/test/{value}` | Public request, route-part, view, and renderer examples. |
| `/stuff` | `/stuff/authenticate`, `/stuff/items`, `/stuff/item/{id}` | Session-protected item-management example backed by the shared Stuff storage component. |

The route middleware maps `/` to the same route parts as `/sandbox/test`. A request to `/sandbox/test/{value}` exposes `test` as route part 2 and `{value}` as route part 3.

The Stuff pages demonstrate authentication, item listing and nesting, adding and editing, deletion, and search. Their database queries live in the `webservco/app-stuff-common` dependency; this repository does not contain a schema or migration for its `stuff_item` table.

## Runtime configuration

The web and CLI bootstrap scripts load `config/.env.ini`. That file is ignored by Git. `config/.env.development.ini` is a development example, not the filename loaded at runtime. Copy or create the local runtime file and set values appropriate to the environment; never copy development credentials into a deployed environment.

The code reads configuration keys including `ENVIRONMENT`, `ALLOWED_HOSTS`, `BASE_URL`, `API_KEY`, `API_VERSION`, `JWT_SECRET`, `AUTHENTICATION_PASSWORD`, database connection values, and session cookie and lifetime values. The configuration component stores these as `APP_`-prefixed `$_SERVER` entries and exposes typed getters.

## How to use this guide

1. Trace a request in [Application architecture](application-architecture.md).
2. Follow the matching recipe in [Feature workflows](feature-workflows.md).
3. Check the package-level responsibilities in [Component reference](components.md).
4. Apply the repository conventions and review the caveats in [Coding standards and development](coding-standards.md).
5. Keep route, controller, view, template, and dependency-container changes aligned. A working page commonly requires changes in more than one of those layers.
