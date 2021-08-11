# docker setup script for CakePHP 4.0
# 01 April 2021, draconigen@gmail.com

# get the latest supported php version bundle apache
FROM php:8.0-apache

# install required packages for composer and deployment
RUN apt-get update && \
	apt-get install -y git zip unzip smbclient

# enable apache mod_rewrite
RUN a2enmod rewrite && \
	service apache2 restart

# install requirements for composer and cakephp
# automated by mlocati/docker-php-extension-installer
# * zip is required by composer
# * mbstring, intl and SimpleXML are required by cakephp
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions zip mbstring intl SimpleXML && \
	install-php-extensions @composer

WORKDIR /var/www/html

# create cakephp app in html root directory
RUN composer create-project --no-interaction --prefer-dist cakephp/app:~4.0 .

# install additional cakephp plugins required specifically by this application 
RUN composer require "cakephp/authentication:^2.0"
#RUN composer require "alt3/cakephp-swagger" # removed because it requires php >= 8.0.0, which is not available yet

# copy application files
COPY . /var/www/html

# set permissions on sqlite database file
RUN chown -R www-data:www-data data/

EXPOSE 80

# defy the purpose of this docker container and deploy all contents onto a bare server
WORKDIR /var/www/html
RUN smbclient '//192.168.0.2/web/' -U '{USER}%{PASS}' -c 'prompt OFF;recurse ON;cd www.dogpixels.net/efo2021/;mput *'