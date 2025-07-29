FROM php:8.2-cli

# Instala apenas utilitários necessários (sem repetir o curl)
RUN apt-get update && apt-get install -y zip unzip

# Copia seu projeto para o container
COPY . /app
WORKDIR /app

# Expõe a porta para o Render
EXPOSE 8000

# Inicia o servidor PHP
CMD ["php", "-S", "0.0.0.0:8000"]
