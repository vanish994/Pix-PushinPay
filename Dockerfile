# Usa imagem oficial do PHP com suporte a servidor embutido
FROM php:8.2-cli

# Instala dependências adicionais se necessário (curl, etc.)
RUN apt-get update && apt-get install -y curl zip unzip && docker-php-ext-install curl

# Copia o projeto para dentro do container
COPY . /app
WORKDIR /app

# Expõe a porta 8000 (Render irá mapear automaticamente)
EXPOSE 8000

# Inicia o servidor PHP embutido na porta correta
CMD ["php", "-S", "0.0.0.0:8000"]
