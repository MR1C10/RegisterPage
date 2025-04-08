# Imagem base com Apache + PHP
FROM php:8.2-apache

# Instala o Composer copiando da imagem oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Cria diretório do projeto dentro do container
WORKDIR /var/www/html

# Copia tudo do projeto para dentro do container
COPY ./form-service /var/www/html

# Define o DocumentRoot como a pasta 'public'
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Roda o composer install na pasta onde está o composer.json
RUN composer install

# Ajusta permissões para o Apache
RUN chown -R www-data:www-data /var/www/html

# Expõe a porta 80
EXPOSE 80