FROM php:7.4.20-apache


ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions gd xdebug mysqli

COPY .htaccess /var/www/html

RUN chmod -R 777 /var/www/html