# DDEV: misc

## Upgrading database

Example: upgrade MariaDB form 10.4 to 10.8

### The easy way

- change database version in DDEV configuration, restart;
- If errors ("Unable to start project because the configured database type does not match the current actual database"), go for the hard way.

### The hard way

- backup yourself any databases you want to keep, DDEV will destroy all;
    - use phpMyAdmin or CLI, [DDEV snapshots will not work](https://ddev.readthedocs.io/en/latest/users/extend/database-types/#caveats)
- `ddev start` (do not change configuration by hand);
- `ddev debug migrate-database mariadb:10.8`
    - DDEV will promise to import back the databases however:
        - it will only handle the `db` database, any custom databases will only be destroyed;
        - even for the `db` the import will sometimes fail;

### Other solutions

- [Untested: steps described in this bug report](https://github.com/ddev/ddev/issues/4089)

---
