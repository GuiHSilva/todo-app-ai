# Todo App

Aplicação simples de lista de tarefas construída com Laravel. Integração com o Gemini para sugestões.

## Requisitos

- Docker

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
docker run --rm -p 8000:80 todo-app-ai
```

Acesse a aplicação em: http://localhost:8000
