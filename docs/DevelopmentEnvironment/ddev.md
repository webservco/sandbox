# ddev

- ["Docker-based PHP development environments."](https://ddev.com/)

> Please see project source code for an example implementation.

## Setup

```shell
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

---

## Usage


```shell
# Start
ddev start

# Launch app
ddev launch

# Launch phpMyAdmin
ddev launch -p

# Launch Mailhog
ddev launch -m

# Show details
ddev status

# Connect to MySQL via command line from host
# Find post ("Show details")
mysql --protocol tcp -h 127.0.0.1 -P __PORT__ -u db -p

# Run command in ddev environment
ddev exec __COMMAND__

# Connect to ssh
ddev ssh

# View logs in real time
ddev logs --follow
```
