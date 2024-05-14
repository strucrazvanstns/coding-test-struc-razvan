FROM php:8.3-cli

# Set working directory
WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install zip bcmath
#Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Copy the local files to the working directory
COPY . /app

#Install composer
RUN composer install
