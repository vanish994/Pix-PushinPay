FROM php:8.2-cli

# Instala dependências básicas
RUN apt-get update && apt-get install -y zip unzip

# Define diretório de trabalho
WORKDIR /app

# Copia os arquivos para o container
COPY . .

# Expõe a porta padrão
EXPOSE 8000

# Inicia o servidor embutido do PHP
CMD ["php", "-S", "0.0.0.0:8000"]
