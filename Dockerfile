FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y curl zip unzip git 

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Get latest Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*