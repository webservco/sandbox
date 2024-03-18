# PHP: Sandbox: index

## Sandbox functionality

### API module

- make sure to set `Authorization` header ("Bearer `API_KEY`");
- use `API_KEY` from configuration file;

- https://sandbox.ddev.site/api/v1/about
- https://sandbox.ddev.site/api/v1/version

### Test module

- Web: https://sandbox.ddev.site/sandbox/test
- Web: https://sandbox.ddev.site/sandbox/test/{SOME_STRING}
- CLI: `ddev exec bin/sandbox-test`

### Stuff module

- https://sandbox.ddev.site/stuff/items