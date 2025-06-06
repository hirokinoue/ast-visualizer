FROM php:8.2-cli-bullseye

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /usr/local/ast-visualizer

COPY . /usr/local/ast-visualizer

ARG UID
ARG GID

RUN apt-get update && apt-get install -y --no-install-recommends \
        zip unzip git vim \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pcntl opcache
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN cp /usr/local/ast-visualizer/php.ini /usr/local/etc/php/php.ini
RUN groupadd -g $GID appgroup || true && \
    useradd -u $UID -g $GID -m appuser || true
RUN chown $UID:$GID /usr/local/ast-visualizer

USER appuser

CMD ["/bin/bash"]
