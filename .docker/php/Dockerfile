FROM alpine:latest

ARG NGINX_UNIT_VERSION=1.26.1

ENV APP_USER app
ENV APP_USER_ID 1001
ENV APP_USER_GROUP_ID 1001

RUN apk add --no-cache curl git shadow openssh-client libstdc++ libssl1.1 pcre2

## Install PHP and NGINX Unit
RUN apk add --no-cache \
    php7 \
    php7-bcmath \
    php7-calendar \
    php7-common \
    php7-ctype \
    php7-curl \
    php7-dom \
    php7-ffi \
    php7-fileinfo \
    php7-gettext \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pcntl \
    php7-pdo \
    php7-pdo_mysql \
    php7-phar \
    php7-posix \
    php7-mongodb \
    php7-mysqli \
    php7-mysqlnd \
    php7-session \
    php7-simplexml \
    php7-soap \
    php7-sodium \
    php7-sockets \
    php7-tokenizer \
    php7-xml \
    php7-xmlreader \
    php7-xmlwriter \
    php7-zip \
    php7-zlib \
    php7-pecl-redis \
    php7-pecl-yaml \
    php7-pecl-xdebug \
    unit-php7

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

## Forwand logs into docker stdout
RUN ln -sf /dev/stdout /var/log/unit.log

COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh \
    && mkdir /docker-entrypoint.d

ENTRYPOINT ["/docker-entrypoint.sh"]

STOPSIGNAL SIGTERM

WORKDIR /var/www/html

CMD ["unitd", "--no-daemon"]
