FROM --platform=linux/x86_64 ubuntu:22.04

ARG CONF_NAME
ARG DIR_HEALTH

USER root

RUN apt-get update
ARG DEBIAN_FRONTEND=noninteractive

# Install packages and remove default server definition
RUN apt-get install -y \
    --no-install-recommends php8.1 \
    php8.1-fpm \
    php8.1-xml \
    php8.1-curl \
    php8.1-zip \
    php8.1-soap \
    php8.1-intl \
    php8.1-mbstring \
    php8.1-gd \
    php8.1-pdo \
    php8.1-mysqli \
    nginx \
    curl \
    ca-certificates \
    supervisor \
    git

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN touch /var/log/php8.1-fpm.log
RUN touch /var/log/php8.1.slow.log
RUN chown www-data /var/log/php8.1-fpm.log
RUN chown www-data /var/log/php8.1.slow.log

# Setup document root
WORKDIR /var/www

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/ace.conf /etc/nginx/sites-available/default
COPY config/fpm-pool.conf /etc/php/8.1/fpm/pool.d/www.conf

# Configure PHP-FPM
COPY config/fpm-pool.conf /etc/php/8.1/fpm/pool.d/www.conf
COPY config/fpm-pool.conf /etc/php/8.1/php-fpm.d/www.conf
COPY config/php.ini /etc/php/8.1/fpm/php.ini

# Configure supervisord
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN chown -R www-data /var/www /run /var/lib/nginx /var/log/nginx

# Switch to use a non-root user from here on
#USER nobody
USER www-data

# Add application
COPY --chown=www-data . /var/www

RUN service php8.1-fpm stop
RUN service php8.1-fpm start
RUN composer config -g github-oauth.github.com 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
RUN composer install

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
