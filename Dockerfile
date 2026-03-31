# Use PHP + Apache
FROM php:8.3-apache

# Apache mods
RUN a2enmod rewrite headers expires

# PHP Ext
RUN apt-get update && apt-get install -y \
        curl \
        pkg-config \
        libcurl4-gnutls-dev \
        libxml2-dev \
        libzip-dev \
        zlib1g-dev \
	libiconv-hook-dev \ 
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install curl mysqli xml zip iconv gd \
    && pecl install apcu \
    && docker-php-ext-enable apcu

# PHP ioncube
RUN curl -o ioncube_loaders_lin_x86-64.tar.gz http://downloads3.ioncube.com/loader_downloads/ioncube_loaders_lin_x86-64.tar.gz \
    && tar xzf ioncube_loaders_lin_x86-64.tar.gz -C /usr/local \
    && rm ioncube_loaders_lin_x86-64.tar.gz

# php.ini
RUN echo 'display_errors = On' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'zend_extension = /usr/local/ioncube/ioncube_loader_lin_8.3.so' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'apc.enabled = 1' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'apc.shm_size = 128M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'apc.enable_cli = 1' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'max_execution_time = 30' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/custom.ini \
    && echo 'date.timezone = "UTC"' >> /usr/local/etc/php/conf.d/custom.ini  


# Copy project files
COPY ./core ./core


# Entry
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

# Apache
CMD ["apache2-foreground"]

# Expose apache
EXPOSE 80


