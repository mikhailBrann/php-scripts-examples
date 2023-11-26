FROM php:7.4-apache

RUN apt-get update

# установка пакетов для сервера
RUN apt-get install -fyqq \
    bash curl wget rsync ca-certificates openssl ssh git apache2-utils
# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
 && chmod 755 /usr/bin/composer
# PHP libraries
RUN docker-php-ext-install bcmath pdo_mysql
# ZIP
RUN apt-get install -fyqq zip libzip-dev \
 && docker-php-ext-install zip \
 && apt-get remove -fyqq libzip-dev

# копируем файлы для конфигурации сервера
COPY ./configs/server/htpass/.htpasswd /etc/apache2/.htpasswd
COPY ./configs/server/site-conf/application.local.conf /etc/apache2/sites-available
COPY ./configs/server/apache-conf/apache2.conf /etc/apache2/apache2.conf

# активируем конфигурацию сайта
RUN a2ensite application.local.conf
RUN a2dissite 000-default.conf
# запускаем редиректы для public
RUN a2enmod rewrite
# релоадим сервер
RUN /etc/init.d/apache2 restart



