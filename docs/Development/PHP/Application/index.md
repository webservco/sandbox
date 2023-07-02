# PHP: Application: index

## Notes

### Apache

- Application is setup to use Apache web server (uses .htaccess file for front controller management).

## Articles

- [Application setup](application_setup.md)
- [Project structure](application_structure.md)

---

## Notes

### Refactoring ideas

- Move some common code to a general library to have lighter applications

#### `ApplicationFactoryFactory`

- use interfaces for `RequestHandlerFactory`, `SpecificModuleControllerInstantiator`;
