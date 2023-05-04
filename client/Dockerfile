FROM php:8-cli

COPY modules.sh /
RUN /modules.sh

WORKDIR /var/www

COPY ./ ./

RUN curl -s https://getcomposer.org/installer | php
RUN php -d memory_limit=-1 composer.phar self-update
RUN php -d memory_limit=-1 composer.phar install
RUN php -d memory_limit=-1 composer.phar dump-autoload --optimize --classmap-authoritative

RUN mkdir -p var/cache var/logs

ENTRYPOINT ["tail", "-f", "/dev/null"]
CMD []
