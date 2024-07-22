# webservco/sandbox

A rudimentary sandbox project for modern web development with PHP 8.

---

## Documentation

### View online 

[webservco.github.io/sandbox/](https://webservco.github.io/sandbox/)

### Serve locally

```shell
python3 -m venv .venv
source .venv/bin/activate
pip install mkdocs
mkdocs serve
deactivate
```

- navigate to [http://127.0.0.1:8000/](http://127.0.0.1:8000/)

### Deploy

```shell
mkdocs gh-deploy
rm -rf site/*
```

