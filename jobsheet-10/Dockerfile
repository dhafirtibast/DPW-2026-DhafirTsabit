FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

EXPOSE 10000

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/g\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/g\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
