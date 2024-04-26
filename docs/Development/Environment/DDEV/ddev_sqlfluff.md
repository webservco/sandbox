# DDEV: SQLFluff

`.ddev/web-build/Dockerfile`

```shell
# Install SQLFluff SQL linter. https://stackoverflow.com/a/77453453/14583382
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"
RUN pip install sqlfluff
```

`.ddev/commands/host/sql-lint`

```shell
#!/bin/bash

## Description: Lint SQL files using SQLFluff
## Usage: sql-lint
## Example: ddev sql-lint

ddev exec sqlfluff lint --dialect mysql /var/www/html/resources/storage/
```

`# .ddev/commands/host/sql-fix`

```shell
#!/bin/bash

## Description: Fix SQL files using SQLFluff
## Usage: sql-fix
## Example: ddev sql-fix

ddev exec sqlfluff fix --dialect mysql /var/www/html/resources/storage/
```