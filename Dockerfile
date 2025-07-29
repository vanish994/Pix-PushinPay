# Usa imagem com Apache + PHP prontos
FROM php:8.2-apache

# Copia todos os arquivos para o diretório servido pelo Apache
COPY . /var/www/html/

# Permissões (opcional)
RUN chown -R www-data:www-data /var/www/html

# Ativa mod_rewrite se quiser usar URLs amigáveis
RUN a2enmod rewrite

# Expõe a porta padrão do Apache
EXPOSE 80
