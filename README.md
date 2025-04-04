# Todo App AI

Aplicação simples de lista de tarefas construída com Laravel. Integração com o Gemini para sugestões.

## Requisitos

- Docker

## Chave de API
Gere sua chave no [Google AI Studio](https://aistudio.google.com/apikey)

## Executar direto pela imagem Docker
```bash
docker run --rm -p 80:80 -e GEMINI_API_KEY={gemini_apy_key_goes_here} guihsilva/todo-app-ai
```

Acesse a aplicação em http://localhost/

## Instalação

Clone o repositório:

```bash
git clone https://github.com/GuiHSilva/todo-app-ai.git
cd todo-app-ai
```

## Execução
Execute o projeto com Docker:

```bash
chmod +x docker-entrypoint.sh
docker build -t todo-app-ai .
docker run --rm -e GEMINI_API_KEY={gemini_apy_key_goes_here} -p 8001:80 todo-app-ai
```
Se preferir, mapeie o volume e insira manualmente a chave no .env

Acesse a aplicação em: http://localhost:8000
