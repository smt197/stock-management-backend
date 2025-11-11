FROM serversideup/php:8.3-fpm-nginx

# Switch to root to install dependencies
USER root

# Install SQLite
RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Switch back to www-data user
USER www-data

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY --chown=www-data:www-data composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

# Copy application files
COPY --chown=www-data:www-data . .

# Create storage and bootstrap cache directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Create SQLite database directory
RUN mkdir -p database && touch database/database.sqlite && chmod 664 database/database.sqlite

# Set proper permissions
RUN chown -R www-data:www-data storage bootstrap/cache database

# Copy entrypoint script
COPY --chown=www-data:www-data scripts/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
