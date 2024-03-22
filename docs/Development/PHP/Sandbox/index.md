# PHP: Sandbox: index

## Sandbox functionality

---

### API module

Generate JWT: https://jwt.io/#debugger-io

Header:
```text
{
  "alg": "HS256",
  "typ": "JWT"
}
```

Payload:
```text
{
  "sub": "A_USER_ID",
  "iss": "API_KEY_FROM_CONFIGURATION_FILE"
}
```

- Verify signature: `your-256-bit-secret`: `JWT_SECRET` from configuration file;

- set `Authorization` header ("Bearer `JWT`");

- https://sandbox.ddev.site/api/v1/about
- https://sandbox.ddev.site/api/v1/version

---

### Test module

- Web: https://sandbox.ddev.site/sandbox/test
- Web: https://sandbox.ddev.site/sandbox/test/{SOME_STRING}
- CLI: `ddev exec bin/sandbox-test`

---

### Stuff module

- https://sandbox.ddev.site/stuff/items