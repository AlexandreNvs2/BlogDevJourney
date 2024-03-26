FROM php:8.2-apache

# Configuration du ServerName pour éviter des avertissements d'Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Mise à jour des paquets et installation des dépendances
RUN apt-get update && apt-get install -y --no-install-recommends \
  apt-utils \
  curl \
  git \
  g++ \
  libicu-dev \
  libpng-dev \
  libxml2-dev \
  libzip-dev \
  libonig-dev \
  libxslt-dev \
  unzip \
  locales \
  && rm -rf /var/lib/apt/lists/*

# Configuration des locales
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
  && echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen \
  && locale-gen

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation de Node.js
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get update \
  && apt-get install -y nodejs \
  && rm -rf /var/lib/apt/lists/*

# Installation de Symfony CLI
RUN curl -sL https://github.com/symfony/cli/releases/download/v4.25.4/symfony_linux_amd64.gz -o symfony.gz \
  && gunzip symfony.gz \
  && chmod +x symfony \
  && mv symfony /usr/local/bin/symfony


# Configuration et installation des extensions PHP
RUN docker-php-ext-configure intl \
  && docker-php-ext-install pdo pdo_mysql opcache intl zip calendar dom mbstring gd xsl

# Installation et activation de l'extension APCu
RUN pecl install apcu \
  && docker-php-ext-enable apcu

WORKDIR /var/www/
