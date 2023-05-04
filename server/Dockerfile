FROM php:8-apache

ARG OPCODE_ENABLED=1
COPY modules.sh /
RUN /modules.sh

WORKDIR /var/www

RUN chown -R www-data:www-data .

COPY --chown=www-data:www-data ./ ./

USER www-data

RUN curl -s https://getcomposer.org/installer | php
RUN php -d memory_limit=-1 composer.phar self-update
RUN php -d memory_limit=-1 composer.phar install
RUN php -d memory_limit=-1 composer.phar dump-autoload --optimize --classmap-authoritative

RUN mkdir -p var/cache var/logs
RUN chown -R www-data:www-data var/cache var/logs

RUN rm -Rf html && mv public html
