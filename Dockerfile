FROM php:7-fpm

# Deps for building extensions
RUN apt-get update && apt-get install -y git libmemcached-dev zlib1g-dev

# Install memcached extension
RUN git clone https://github.com/php-memcached-dev/php-memcached /usr/src/php/ext/memcached \
  && cd /usr/src/php/ext/memcached && git checkout -b php7 origin/php7 \
  && docker-php-ext-configure memcached \
  && docker-php-ext-install memcached

# Install the zip extension for composer
RUN docker-php-ext-install zip

# Install the mysql extension
RUN docker-php-ext-install pdo pdo_mysql

ADD docker/php/php.ini /usr/local/etc/php/php.ini

# Download and run Composer
RUN apt-get install -y wget
RUN wget -qO- https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/sbin
RUN apt-get remove -y --purge wget git
RUN apt-get clean

ADD . /var/www/html
RUN /usr/local/sbin/composer install --prefer-source --optimize-autoloader --no-interaction
RUN chown -R www-data:www-data /var/www/html