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

- `src/Project/Instantiator/Controller` create main
- `src/Project/Instantiator/Controller/SpecificModuleControllerInstantiator.php` edit

---

## Work

- **next**: create service(s) to retrieve:
    - item
    - list of items
- **then** figure out how to inject the service(s) in `src/Project/Controller/Service/Stuff/ItemController.php`

---

## Temp: Add a new module (section)

- create: `src/Project/Instantiator/Controller/`
- (if special dependencies): create: `src/Project/Factory/Container/`

## Temp: Add a new page in an existing section.

- define what you want to display: create view (implements ViewInterface)
- create template: display view
- (if working with storage): add interface method(s)
- (if working with storage): add storage method(s)
- create controller
- add route (config dir)

---

## Temp: From workflow

- (if need custom method(s)) create from factory interface (extends FormFactoryInterface)
- create form factory (FormFactoryInterface)
- add/update FormFactoryContainerInterface
- add/update FormFactoryContainer
- controller: createAndHandleForm
- controller: handle form sent/valid
- view: add form

---

## TODO: add a new section (abstract stuff, routing, storage, etc)

- example: create a blog
