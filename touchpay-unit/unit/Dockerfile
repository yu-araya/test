# for docker-compose. context=touchpay-package directory.
FROM php:5.6-apache

RUN docker-php-ext-install pdo_mysql mysqli mbstring
RUN a2enmod rewrite
COPY /unit/httpd_conf/apache2.conf /etc/apache/apache2.conf
COPY /unit/php.ini /usr/local/etc/php/php.ini
COPY /unit/start.sh /start.sh
RUN chmod 777 /start.sh

## Add the wait script to the image
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/2.7.3/wait /wait
RUN chmod +x /wait

CMD /wait && /start.sh