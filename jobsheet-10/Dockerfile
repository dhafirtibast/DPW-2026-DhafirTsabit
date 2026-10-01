FROM php:8.1-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

# Pastikan hanya satu MPM (prefork) termuat, mencegah error
# "AH00534: apache2: Configuration error: More than one MPM loaded."
RUN a2dismod mpm_event 2>/dev/null || true; \
    a2dismod mpm_worker 2>/dev/null || true; \
    a2enmod mpm_prefork

COPY . /var/www/html/

EXPOSE 10000

CMD ["sh", "-c", "sed -i \"s/Listen 80/Listen ${PORT:-10000}/g\" /etc/apache2/ports.conf && sed -i \"s/:80>/:${PORT:-10000}>/g\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
