# DDEV: project setup

```sh
ddev config
```

## Customize

### `.ddev/config.yaml`

```yaml
php_version: "8.2"
webserver_type: apache-fpm
database:
    type: mariadb
    version: "10.8"
timezone: Europe/Rome
webimage_extra_packages: [php-ast]
```

### `.ddev/php/99-custom.ini`

```ini
; Custom php configuration

; Log all errors
error_reporting = E_ALL
; But do not display them in the frontend
display_errors = Off
```

### `.ddev/docker-compose.mounts.yaml`

- Note: make sure the directory `p` exists in the home path.

```yaml
version: '3.6'
services:
  web:
    volumes:
      - type: bind
        # source = path on host
        source: $HOME/p
        # target = path in container
        target: $HOME/p
```
