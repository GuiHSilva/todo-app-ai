#!/usr/bin/env bash

# Verifica se a chave Gemini está definida
if [ -z "$GEMINI_API_KEY" ]; then
    echo "Erro: A chave Gemini não está definida."
    exit 1
fi

export GEMINI_API_KEY

# Garante que as variáveis de ambiente são acessíveis pelo Laravel
# Adiciona a chave Gemini ao arquivo .env com quebra de linha adequada
echo >> /var/www/html/.env  # Garante que há uma quebra de linha no final do arquivo existente
echo "GEMINI_API_KEY=\"${GEMINI_API_KEY}\"" >> /var/www/html/.env

php artisan config:clear
php artisan cache:clear
php artisan serve --host=0.0.0.0 --port=80