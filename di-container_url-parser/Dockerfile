FROM php:8.2-fpm-alpine
ARG user
ARG uid

# Install shadow to provide useradd
RUN apk add --no-cache shadow

# Install Composer
RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer && chmod +x /usr/local/bin/composer

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www
RUN chown -R www-data:www-data /var/www/
RUN chmod -R 775 /var/www/

USER $user