FROM php:8.4-bookworm AS base

ARG APP_UID=33
ARG APP_GID=33

RUN groupmod -g ${APP_GID} www-data && \
    usermod -u ${APP_UID} -g ${APP_GID} www-data && \
    mkdir -p /usr/src/app && \
    chown www-data:www-data /usr/src/app

RUN apt-get update && apt-get install -y \
    libssl-dev \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    apcu \
    bcmath \
    intl \
    opcache \
    zip \
    sockets \
    pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer global require spiral/roadrunner-cli \
    --no-ansi \
    --prefer-dist \
    --no-progress \
    --no-interaction && \
    ~/.composer/vendor/bin/rr get-binary --location /usr/local/bin

WORKDIR /usr/src/app
EXPOSE 8080
ENTRYPOINT ["rr"]

FROM base AS development

RUN install-php-extensions xdebug-3.4.3

ENV XDEBUG_MODE=develop,debug
ENV XDEBUG_CONFIG="client_host=host.docker.internal idekey=PHPSTORM"
ENV PHP_IDE_CONFIG="serverName=restaurateur@docker"

USER www-data
CMD ["serve", "-c", ".rr.dev.yaml", "--debug"]

FROM base AS production

COPY composer.json composer.lock ./

RUN composer install \
    --no-ansi \
    --no-dev \
    --no-scripts \
    --prefer-dist \
    --no-progress \
    --no-interaction \
    --optimize-autoloader

COPY --chown=www-data:www-data . .

ENV APP_ENV=prod

USER www-data

RUN composer check-platform-reqs \
    && php bin/console cache:warmup

CMD ["serve"]
