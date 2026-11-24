# PHP 8.1 is the minimum version the SDK supports (composer.json "php": "^8.1.0"):
# keep this pinned so code is always tested against it.
FROM php:8.1-cli

ARG UID=1000
ARG GID=1000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN groupadd --gid "${GID}" app \
    && useradd --uid "${UID}" --gid "${GID}" --create-home --shell /bin/bash app \
    && mkdir -p /app /home/app/.composer \
    && chown app:app /app /home/app/.composer

ENV COMPOSER_HOME=/home/app/.composer

USER app
WORKDIR /app

CMD ["bash"]
