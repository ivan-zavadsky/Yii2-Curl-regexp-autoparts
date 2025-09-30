FROM php:8.2-fpm

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apt-get update
RUN apt-get install bash
RUN apt-get install curl
RUN yes | apt-get install zip

RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer create-project --prefer-dist yiisoft/yii2-app-basic yii2


#RUN pecl install xdebug && docker-php-ext-enable xdebug