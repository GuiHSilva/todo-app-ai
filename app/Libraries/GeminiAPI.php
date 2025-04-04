<?php

namespace App\Libraries;

use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;

class GeminiAPI implements ApiContract
{
    public function __construct(
        protected Client $client,
    ) {
        $this->client = new Client(config('services.gemini.api_key'));
    }

    public function todoSuggestionsFor(string $description): string
    {
        $response = $this->client->withV1BetaVersion()
            ->generativeModel(ModelName::GEMINI_1_5_FLASH)
            ->withSystemInstruction('Você é um assistente especializado em gerar sugestões de tarefas úteis e práticas em formato JSON. Seu objetivo é ajudar os usuários a planejar atividades de forma eficiente. Formate sua resposta EXCLUSIVAMENTE como um JSON válido contendo um array de objetos, onde cada objeto tem duas propriedades: "title" (título curto da tarefa) e "description" (descrição mais detalhada). Gere entre 5 a 7 tarefas específicas. Não inclua nenhum texto fora do JSON. A resposta deve começar com { e terminar com } sem nenhum texto adicional.')
            ->generateContent(
                new TextPart("Com base no seguinte objetivo: \"$description\", gere um JSON válido contendo um array de objetos de tarefas. Cada objeto deve ter os campos 'title' (título curto) e 'description' (descrição detalhada). O formato deve ser: {\"tasks\": [{\"title\": \"Título 1\", \"description\": \"Descrição 1\"}, ...]}. Gere entre 5 a 7 tarefas específicas e bem definidas. Retorne EXCLUSIVAMENTE o JSON válido, sem texto adicional."),
            );

        // Retorna o conteúdo da resposta como string
        return $response->text();
    }
}