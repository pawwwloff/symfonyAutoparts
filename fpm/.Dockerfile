ARG PHP_VERSION=7.2
ARG NGINX_VERSION=1.15

# FOR PHP
FROM php:${PHP_VERSION}-fpm-alpine as php_fpm

# persistent / runtime deps
RUN apk add --no-cache \
        $PHPIZE_DEPS \
		acl \
		file \
		gettext \
		git \
		libpng-dev \
		libjpeg-turbo-dev \
		freetype-dev \
		bzip2-dev \
		icu-dev \
		openjdk8 \
        libreoffice \
        openssl-dev \
		#libsodium-dev \
		#rabbitmq-c-dev \
		supervisor \
	; \
    docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
        && docker-php-ext-install bz2 \
        && docker-php-ext-install opcache \
        && docker-php-ext-install intl \
        && docker-php-ext-install zip \
        && docker-php-ext-install pdo_mysql \
       # && docker-php-ext-install sodium \
        && docker-php-ext-install bcmath \
        && docker-php-ext-install sockets
       # && pecl install amqp \
       # && docker-php-ext-enable amqp

RUN pecl install mongodb-1.5.3 \
    && echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/docker-php-ext-mongodb.ini \
    && pecl install apcu-5.1.12 \
    && echo "extension=apcu.so" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

