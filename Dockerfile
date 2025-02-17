# Usa a imagem do PHP com Apache
FROM php:8.1-apache

# Instala extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copia os arquivos do seu projeto para o servidor
COPY . /var/www/html/

# Expõe a porta 80
EXPOSE 80
