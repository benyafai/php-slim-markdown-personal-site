FROM composer:latest as build-stage
COPY ./ /src
WORKDIR /src
RUN composer update
RUN composer install --no-dev

FROM php:8-apache as production-stage
COPY --from=build-stage /src /src

RUN rm -rf /var/www/html && mv /src /var/www/html && \
    chown -R www-data:www-data /var/www/html/ && \
    chmod -R 644 /var/www/html && \
    find /var/www/html/ -type d -exec chmod 755 {} \;

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite \
    && echo "ServerTokens Prod\n" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off\n" >> /etc/apache2/apache2.conf \
    && echo "php_admin_flag display_errors off\n" >> /etc/apache2/apache2.conf \
    && echo "php_admin_flag expose_php off\n" >> /etc/apache2/apache2.conf
RUN service apache2 restart