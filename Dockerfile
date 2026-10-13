# Supported PHP versions: 8.2, 8.3, 8.4, 8.5
ARG PHP_VERSION=8.4
# PHP 8.5 support requires compatible extension builds (check install-php-extensions)

###########################################
# Composer dependencies stage
###########################################
FROM composer:latest AS composer-deps

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install composer dependencies (no autoloader yet, will optimize in final stage)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-ansi \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs

###########################################
# Main application stage
###########################################
FROM php:${PHP_VERSION}-cli-alpine

# Adapted from exaco/laravel-octane-dockerfile (MIT licence).
LABEL maintainer="Novixa <info@novixa.dev>"
LABEL org.opencontainers.image.title="Novixa Aqar"
LABEL org.opencontainers.image.description="Novixa Aqar real-estate platform (Laravel Octane / RoadRunner)"
LABEL org.opencontainers.image.vendor="Novixa"
LABEL org.opencontainers.image.url="https://novixa.dev"
LABEL org.opencontainers.image.source=https://github.com/Novixa-dev/novixa-aqar

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG TZ=UTC

ENV TERM=xterm-color \
    WITH_HORIZON=false \
    WITH_SCHEDULER=false \
    WITH_REVERB=false \
    OCTANE_SERVER=roadrunner \
    USER=octane \
    ROOT=/var/www/html \
    COMPOSER_FUND=0 \
    COMPOSER_MAX_PARALLEL_HTTP=24

WORKDIR ${ROOT}

SHELL ["/bin/sh", "-lc"]

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime \
  && echo ${TZ} > /etc/timezone

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install system dependencies and PHP extensions in one layer
RUN apk add --no-cache \
    curl \
    wget \
    nano \
    ncdu \
    procps \
    psmisc \
    ca-certificates \
    supervisor \
    su-exec \
    libsodium-dev && \
    install-php-extensions \
    bz2 \
    pcntl \
    mbstring \
    bcmath \
    sockets \
    opcache \
    exif \
    pdo_mysql \
    zip \
    intl \
    gd \
    redis \
    igbinary && \
    docker-php-source delete && \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

RUN arch="$(apk --print-arch)" \
    && case "$arch" in \
    armhf) _cronic_fname='supercronic-linux-arm' ;; \
    aarch64) _cronic_fname='supercronic-linux-arm64' ;; \
    x86_64) _cronic_fname='supercronic-linux-amd64' ;; \
    x86) _cronic_fname='supercronic-linux-386' ;; \
    *) echo >&2 "error: unsupported architecture: $arch"; exit 1 ;; \
    esac \
    && wget -q "https://github.com/aptible/supercronic/releases/download/v0.2.29/${_cronic_fname}" \
    -O /usr/bin/supercronic \
    && chmod +x /usr/bin/supercronic \
    && mkdir -p /etc/supercronic \
    && echo "*/1 * * * * php ${ROOT}/artisan schedule:run --no-interaction" > /etc/supercronic/laravel

RUN addgroup -g ${WWWGROUP} ${USER} \
    && adduser -D -h ${ROOT} -G ${USER} -u ${WWWUSER} -s /bin/sh ${USER}

RUN mkdir -p /var/log/supervisor /var/run/supervisor \
    && chown -R ${USER}:${USER} ${ROOT} /var/log /var/run \
    && chmod -R a+rw ${ROOT} /var/log /var/run

RUN cp ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini

USER ${USER}

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy vendor from composer-deps stage for better caching
COPY --chown=${USER}:${USER} --from=composer-deps /app/vendor ./vendor

# Copy composer files (needed for autoloader generation)
COPY --chown=${USER}:${USER} composer.json composer.lock ./

# Copy application code first so autoloader can resolve all files
COPY --chown=${USER}:${USER} . .

# Generate optimized autoloader now that all app files are present
RUN composer dump-autoload --classmap-authoritative --no-dev && \
    composer clear-cache

# Create necessary Laravel directories
RUN mkdir -p \
    storage/framework/sessions \
    storage/framework/views \
    storage/framework/cache \
    storage/framework/testing \
    storage/logs \
    bootstrap/cache && \
    chmod -R a+rw storage

# Copy configuration files
COPY --chown=${USER}:${USER} .docker/supervisord.conf /etc/supervisor/
COPY --chown=${USER}:${USER} .docker/octane/RoadRunner/supervisord.roadrunner.conf /etc/supervisor/conf.d/
COPY --chown=${USER}:${USER} .docker/supervisord.horizon.conf /etc/supervisor/conf.d/
COPY --chown=${USER}:${USER} .docker/supervisord.reverb.conf /etc/supervisor/conf.d/
COPY --chown=${USER}:${USER} .docker/supervisord.scheduler.conf /etc/supervisor/conf.d/
COPY --chown=${USER}:${USER} .docker/supervisord.worker.conf /etc/supervisor/conf.d/
COPY --chown=${USER}:${USER} .docker/php.ini ${PHP_INI_DIR}/conf.d/99-octane.ini
COPY --chown=${USER}:${USER} .docker/start-container /usr/local/bin/start-container
COPY --chown=${USER}:${USER} .docker/healthcheck /usr/local/bin/healthcheck

# Copy environment file
COPY --chown=${USER}:${USER} .env.example ./.env

# Pre-fetch the RoadRunner server binary at build time. Octane's
# `octane:roadrunner` command otherwise lazily downloads it from GitHub's
# API on first boot -- on a shared host like Railway that call comes back
# HTTP 403 (GitHub's anonymous API rate limit, exhausted by other tenants
# on the same egress IP), so the octane process crash-looped forever and
# the app never came up. Doing it once here, at build time, means every
# container boot just uses the binary already baked into the image.
RUN mkdir -p /tmp/opcache-file-cache && php artisan octane:install --server=roadrunner --no-interaction

# octane:install leaves an empty .rr.yaml, which lets RoadRunner serve
# public/*.php as plain-text source; replace it with ours.
COPY --chown=${USER}:${USER} .docker/octane/RoadRunner/.rr.prod.yaml ./.rr.yaml

RUN chmod +x /usr/local/bin/start-container /usr/local/bin/healthcheck && \
    cat .docker/utilities.sh >> ~/.bashrc

# Start as root: a host-mounted volume (Railway's storage/app bind mount)
# arrives owned by root regardless of what this image built as, since the
# mount is layered on at container-start time, after the image's own
# ownership no longer matters for that path. start-container fixes the
# mounted volume's ownership as root, then re-execs itself as ${USER} via
# su-exec before running anything else (migrations, seeding, the app
# server) -- so the app itself never actually runs as root, only this
# one-time fixup does.
USER root

EXPOSE 8000
EXPOSE 8080

ENTRYPOINT ["start-container"]

# Mode-aware (http: octane:status; scheduler/worker/horizon/reverb: their
# supervisor program), so every container mode reports real health.
HEALTHCHECK --start-period=60s --interval=15s --timeout=10s --retries=4 CMD healthcheck
