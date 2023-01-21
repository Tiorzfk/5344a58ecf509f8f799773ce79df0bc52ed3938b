FROM php:7.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    wget \
    gnupg2 \
    && docker-php-ext-install pdo_pgsql zip

# RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ main 9.0" > /etc/apt/sources.list.d/pgdg.list
# RUN wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | \
#    apt-key add -
   
RUN apt-get update
# RUN apt-get install -y postgresql-client-9.0
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html
