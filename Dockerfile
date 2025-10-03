FROM php:8.2-apache

# включаем mod_rewrite и настраиваем корень сайта на папку public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# системные зависимости для composer и сборки расширений
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       unzip \
       libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP расширения
RUN docker-php-ext-install pdo pdo_mysql zip

# Устанавливаем Composer глобально из официального образа
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

# по умолчанию запускаем apache
CMD ["apache2-foreground"]
