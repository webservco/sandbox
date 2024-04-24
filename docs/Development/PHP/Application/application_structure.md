# Application: Project structure

## Command

- Code that will be run by CLI

## Container

- DIY Dependency Injection container implementation.
- Creates and keeps a list of single instances (only one instance created).

## Contract

- Interfaces

## Controller

- Can use any nested directory structure (path is not hard coded when resolving controller);
- Receives a request, returns a response;
- Uses the same definition as `Psr\Http\Server\RequestHandlerInterface`;
- Can use a `ViewRendererInterface` to format the output;
- Since using an interface it is agnostic about the type of response it will return; the caller (usually another request handler) will feed it an appropriate view based on the request (eg. `HtmlRenderer`, `JSONRenderer`, etc)

## DataTransfer

- DTO's, ValueObject's, etc

## Factory

- DIY Dependency Injection container implementation.
- Create and return instance (new instance created each time);

## Instantiator

- Special type of Factory that creates objects dynamically.
- Used to instantiate Controllers and related objects.

## Middleware

- Do extra request processing before it is handled by a request handler.

## RequestHandler

- Handle input Request (optionally processed by middleware) return a Response that can be emitted.

## Service

- Project specific services.

## View

- View objects.

## Templates

- `resources/templates`
- template group directories;
- can contain/use multiple;
- individual theme directory structure not hardcoded so free to structure in any way;
- each template group can have multiple layouts (themes?) (handled via one main template) all using the same templates;
    - eg. `main.light`, `main.dark`;

### Vanilla

- `resources/templates/vanilla`
- simple (vanilla) PHP/HTML theme;
