ARG PHP_VERSION
FROM php:$PHP_VERSION-fpm-stretch

ARG PHP_VERSION
ARG MCRYPT_ENABLE
ARG MCRYPT_VERSION
ARG GMP_ENABLE
ARG LIBSODIUM_ENABLE
ARG LIBSODIUM_VERSION
ARG LIBSODIUM_PHP_VERSION
ARG GD_ENABLE
ARG PDO_ENABLE
ARG PDO_MYSQL_ENABLE
ARG PDO_PGSQL_ENABLE
ARG PHPREDIS_ENABLE
ARG PHPREDIS_VERSION
ARG INTL_ENABLE
ARG ZIP_ENABLE
ARG OPCACHE_ENABLE
ARG APCU_ENABLE
ARG APCU_VERSION
ARG AMQP_ENABLE
ARG AMQP_VERSION
ARG XDEBUG_ENABLE
ARG XDEBUG_VERSION
ARG COMPOSER_ALLOW_SUPERUSER
ARG COMPOSER_HOME
ARG CUSTOM_PHP_INI_FILE

RUN apt-get update

# Mcrypt
RUN if [ $MCRYPT_ENABLE = true ]; then \
        apt-get -y install libmcrypt-dev \
            && pecl install mcrypt-$MCRYPT_VERSION \
            && docker-php-ext-enable mcrypt; \
    fi;
# libsodium
RUN if [ $LIBSODIUM_ENABLE = true ]; then \
        mkdir -p /tmpbuild/libsodium && \
            cd /tmpbuild/libsodium && \
            curl -L https://download.libsodium.org/libsodium/releases/libsodium-${LIBSODIUM_VERSION}.tar.gz -o libsodium-${LIBSODIUM_VERSION}.tar.gz && \
            tar xfvz libsodium-${LIBSODIUM_VERSION}.tar.gz && \
            cd /tmpbuild/libsodium/libsodium-${LIBSODIUM_VERSION}/ && \
            ./configure && \
            make && make check && \
            make install && \
            mv src/libsodium /usr/local/ && \
            rm -Rf /tmpbuild/ && \
            pecl install libsodium-$LIBSODIUM_PHP_VERSION; \
    fi;

# gmp
RUN if [ $GMP_ENABLE = true ]; then \
        apt-get install -y libgmp-dev re2c libmhash-dev file \
            && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/ \
            && docker-php-ext-configure gmp  \
            && docker-php-ext-install gmp \
            && docker-php-ext-enable gmp; \
    fi;

# GD
RUN if [ $GD_ENABLE = true ]; then \
        apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
            && docker-php-ext-configure gd  \
                --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
            && docker-php-ext-install -j$(nproc) gd; \
    fi;

# PDO
RUN if [ $PDO_ENABLE = true ]; then \
        docker-php-ext-install -j$(nproc) pdo; \
    fi;

# PDO Mysql
RUN if [ $PDO_MYSQL_ENABLE = true ]; then \
        docker-php-ext-install -j$(nproc) pdo_mysql; \
    fi;

# Postgres PDO
RUN if [ $PDO_PGSQL_ENABLE = true ]; then \
        apt-get install -y libpq-dev \
            && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
            && docker-php-ext-install pdo_pgsql; \
    fi;

# Redis
RUN if [ $PHPREDIS_ENABLE = true ]; then \
        mkdir -p /usr/src/php/ext/redis \
            && curl -L https://github.com/phpredis/phpredis/archive/$PHPREDIS_VERSION.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
            && echo 'redis' >> /usr/src/php-available-exts \
            && docker-php-ext-install redis; \
    fi;

# INTL
RUN if [ $INTL_ENABLE = true ]; then \
        apt-get install -y zlib1g-dev libicu-dev g++ \
          && docker-php-ext-configure intl \
          && docker-php-ext-install intl; \
    fi;

# ZIP
RUN if [ $ZIP_ENABLE = true ]; then \
        apt-get install -y libzip-dev \
          && docker-php-ext-install zip; \
    fi;

# OPCACHE
RUN if [ $OPCACHE_ENABLE = true ]; then \
        docker-php-ext-install -j$(nproc) opcache; \
    fi;

# APCu
RUN if [ $APCU_ENABLE = true ]; then \
       pecl install apcu-$APCU_VERSION \
         && docker-php-ext-enable apcu; \
    fi;

# xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.tmp
RUN if [ $XDEBUG_ENABLE = true ]; then \
        cat /usr/local/etc/php/conf.d/xdebug.ini.tmp >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        pecl install xdebug-$XDEBUG_VERSION && docker-php-ext-enable xdebug; \
    fi;
RUN rm /usr/local/etc/php/conf.d/xdebug.ini.tmp

# AMQP
RUN if [ $AMQP_ENABLE = true ]; then \
       apt-get install -y librabbitmq-dev \
         && pecl install amqp-$AMQP_VERSION \
         && docker-php-ext-enable amqp; \
    fi;

# composer
COPY composer_install.sh /tmp/composer_install.sh
RUN /bin/bash /tmp/composer_install.sh
RUN rm /tmp/composer_install.sh

#copy custom php config
COPY $CUSTOM_PHP_INI_FILE /usr/local/etc/php/conf.d/custom.ini.tmp
RUN cat /usr/local/etc/php/conf.d/custom.ini.tmp >> /usr/local/etc/php/conf.d/custom.ini
RUN rm /usr/local/etc/php/conf.d/custom.ini.tmp

EXPOSE 9000

CMD ["php-fpm"]
