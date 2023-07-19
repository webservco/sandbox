# Docker + PHP + Composer
#
# https://webservco.github.io/sandbox/Development/Environment/Docker/docker_php_composer_cli/
# Minimal Docker setup for PHP library/package/CLI development.
# Full feature example file, edit as needed.

# Use latest PHP CLI image.
FROM php:8.2-cli

# (Optional) Copy custom PHP configuration into the container.
COPY .docker/assets/php/conf.d/ $PHP_INI_DIR/conf.d/

# Update packages and install git.
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

# Install PHP extensions.
# https://github.com/mlocati/docker-php-extension-installer
RUN curl -sSL \
https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
gd zip

# Setup user in the container.
RUN useradd dev
RUN mkdir -p -m 0700 /home/dev && chown -R dev /home/dev

# Setup project files into the container.
RUN mkdir -p -m 0700 /home/dev/project && chown -R dev /home/dev/project
# Copy files.
COPY . /home/dev/project
RUN chown -R dev /home/dev/project

# (Optional) Setup special project dependencies (private repos and whatnot).
RUN mkdir -p -m 0700 /home/dev/p && chown -R dev /home/dev/p
# Problem: no outside files possible, no symlinks, current context only;
# See README for workaround.
# Copy files.
COPY .docker/assets/temp/p/ /home/dev/p/
RUN chown -R dev /home/dev/p

# Copy Composer binary from official image.
COPY --from=composer /usr/bin/composer /usr/bin/composer
# (Optional) Copy custom composer configuration into the container.
RUN mkdir -p -m 0700 /home/dev/.composer && chown -R dev /home/dev/.composer
COPY .docker/assets/home/dev/composer/ /home/dev/.composer

# Set working directory.
WORKDIR /home/dev/project
# Set user that runs the commands.
USER dev

# Install composer dependencies
RUN composer update

# Setup compsoer checks as default command.
CMD ["composer", "all"]
