# Application: Workflow (WIP)

## Notes

- `Controller/Contract/` general interface
- `Controller/Service/` general abstract controller, specific controller
- `resources/templates/vanilla/main/` main template
- `View/` main, specific
- `Factory/View/Container`
- `resources/templates/vanilla/` specific
- `config`

- `RequestHandler/ThreePart/` (why needed?, no customization was required)

- `src/Project/Factory/Http/RequestHandlerFactory.php`
- `src/Project/Factory/Middleware/ResourceMiddlewareFactory.php`

- `src/Project/Instantiator/Controller` create instantiator
- `src/Project/Instantiator/Controller/SpecificModuleControllerInstantiator.php` add line in array

---

### Basic application structure

- (TODO)
- `bin` (if using `Command`);

- `public`
  - `.htaccess`
  - `index.php`
- `resources/templates`;
- `src/Project`;

- `config`;

---

### Create a new module (Command)

- create command: `src/Project/Command/`
- create command factory: `src/Project/Factory/Command/`
- create command runner: `bin/`

---

## Create a new module (Web)

- create: `src/Project/Instantiator/Controller/`
- (if special dependencies): create: `src/Project/Factory/Container/`
- (**TODO**: add other steps)

---

## Add a new page in an existing section.

- (**TODO**: check)
- define what you want to display: create view (implements ViewInterface)
- create template: display view
- (if working with storage): add interface method(s)
- (if working with storage): add storage method(s)
- create controller
- add route (config dir)

---

## Form workflow

- (**TODO**: check)
- (if need custom method(s)) create from factory interface (extends FormFactoryInterface)
- create form factory (FormFactoryInterface)
- add/update FormFactoryContainerInterface
- add/update FormFactoryContainer
- controller: createAndHandleForm
- view: add form

---

## API workflow

### Authentication

- `src/Project/Middleware/ApiAuthenticationMiddleware.php`
- `src/Project/Factory/Http/AbstractRequestHandlerFactory.php`.`createApiAuthenticationMiddleware`
- `src/Project/Factory/Http/RequestHandlerFactory.php`.`createRequestHandler`.`addMiddleware`

### Configuration

- `config/`

### Container

- `src/Project/Contract/Container/API/`
- `src/Project/Container/API/`
- `src/Project/Factory/Container/APILocalDependencyContainerFactory.php`
- `src/Project/Instantiator/Controller/APIModuleControllerInstantiator.php`

### Data Transfer Objects

- `src/Project/DataTransfer/API/`

### Views

- `src/Project/View/API/`

### Templates

- `resources/templates/vanilla/main/main.api.default.php`
- `resources/templates/vanilla/api/default.php`

### Controller

- `src/Project/Controller/API/AbstractAPIController.php`
- `src/Project/Controller/API/APIController.php`

### Routes configuration

- `config/API/Routes.php`

### Middleware

- `src/Project/RequestHandler/ThreePart/ApiRequestHandler.php`
- `src/Project/Factory/Middleware/ResourceMiddlewareFactory.php`
    - `createApiRequestHandler`
    - `getResouceMiddlewareHandlers`

---