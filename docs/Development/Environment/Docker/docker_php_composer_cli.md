# Docker + PHP + Composer

PHP library/package/CLI development without having the required PHP / Composer versions installed locally.

No web interface.

## Setup

Copy needed files from the repository.

Hint: [Download only one specific folder from a remote repository](https://stackoverflow.com/a/18324458):

```sh
# View
svn ls https://github.com/webservco/sandbox.git/trunk/.docker
# Download
svn export https://github.com/webservco/sandbox.git/trunk/.docker
```

---

## Usage

### 1) Copy (snapshot) mode

- Read comments / edit to customize: `.docker/config/php82-cli-copy/Dockerfile`
- Use case: check project in isolation, without adding the vendor dir on local (avoid PHAN errors);
- Files are copied into the container at build time (not updated in real-time);
- Working on a "snapshot" done at the time of the build
- Installs composer dependencies
- Runs all checks by default

```sh

# Build:
BUILD_TEMP_DIR='.docker/assets/temp/';
# Create temporary directory for special project dependencies (local).
mkdir --parents ${BUILD_TEMP_DIR}p/parcelvalue-v3/framework/
# Copy files into the temporary directory (local).
cp --recursive ~/p/parcelvalue-v3/framework/ ${BUILD_TEMP_DIR}p/parcelvalue-v3/
# Build.
docker build -t lhost-v3-framework-copy -f .docker/config/php82-cli-copy/Dockerfile .
# Cleanup temporary directory.
rm -rf ${BUILD_TEMP_DIR}*

# Run: default command: run all checks
docker run -it --rm --name lhost-v3-framework-running lhost-v3-framework-copy

# Run: custom command (default command is not executed)
docker run -it --rm --name lhost-v3-framework-running lhost-v3-framework-copy /bin/bash -c "ls -lah vendor"
```

### 2) Mount (real-time) mode

- Read comments / edit to customize: `.docker/config/php82-cli-mount/Dockerfile`
- Use case: active development, need to run quickly on up-to-date files; need vendor dir (or no problem if present);
- Files are mounted at run time, so no composer commands are run automatically
- Execute any commands needed at run time
- Caveat: This mode is unuseful if using special dependencies;
    - since mounted in a different path in the container, they will not be available on the local filesystem (so not able to run composer commands on local or have code available in vscode for example);

```sh
# Build.
docker build -t lhost-v3-framework-mount -f .docker/config/php82-cli-mount/Dockerfile .

# Run (specify mounts and commands to run)
docker run -it --rm --name lhost-v3-framework-running \
# special dependencies
--mount src="$HOME/p/parcelvalue-v3/framework/",target=/home/dev/p/parcelvalue-v3/framework/,type=bind \
# project code
--mount src=.,target=/home/dev/project,type=bind \
lhost-v3-framework-mount \
/bin/bash -c "composer update && composer all"
```

---

## External resources

- https://hub.docker.com/_/php/
- https://hub.docker.com/_/composer
- https://medium.com/@othillo/adding-composer-to-php-docker-images-using-multi-stage-builds-2a10967ae6c1

---
