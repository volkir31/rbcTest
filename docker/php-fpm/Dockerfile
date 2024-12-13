FROM php:8.4-fpm

RUN apt update && apt install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    bash \
    libpq-dev
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql pgsql zip mbstring exif pcntl bcmath gd
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#RUN curl -sS https://get.symfony.com/cli/installer | bash
#RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

RUN mkdir -p /var/www

RUN chown -R www-data:www-data /var/www