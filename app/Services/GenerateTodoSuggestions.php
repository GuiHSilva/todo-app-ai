<?php

namespace App\Services;

use App\Libraries\ApiContract;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Str;

class GenerateTodoSuggestions
{
    /**
     * Cliente da API que será usado para obter sugestões
     */
    protected $aiClient;

    public function __construct(ApiContract $aiClient)
    {
        $this->aiClient = $aiClient;
    }

    /**
     * Gera sugestões de tarefas baseadas no prompt do usuário
     *
     * @param string $prompt O que o usuário pretende fazer
     * @return array Lista de sugestões de tarefas
     */
    public function getSuggestions(string $prompt): array
    {
        $fullSanitizedPrompt = Str::of($prompt)->trim()->lower()->slug()->replace('"', '');

        return Cache::remember('suggestions_' . $fullSanitizedPrompt, 60, function () use ($fullSanitizedPrompt, $prompt) {
            // Obter sugestões da API
            $suggestionsText = $this->aiClient->todoSuggestionsFor($prompt);

            // Processar o texto em uma array de sugestões
            return $this->processAiResponse($suggestionsText);
        });
    }

    /**
     * Processa a resposta JSON da IA e converte em uma array de sugestões.
     *
     * @param string $response Resposta da IA em formato JSON
     * @return array Array de sugestões processadas
     */
    private function processAiResponse(string $response): array
    {
        try {
            // Limpar qualquer texto que não faça parte do JSON
            $jsonString = trim(preg_replace('/^[\s\S]*?({[\s\S]*})[\s\S]*$/', '$1', $response));

            // Decodificar o JSON
            $jsonData = json_decode($jsonString, true);

            // Verifica se a decodificação foi bem-sucedida
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Falha na decodificação, tentar extrair usando regex mais agressivo
                preg_match('/{.*}/s', $response, $matches);
                if (!empty($matches[0])) {
                    $jsonData = json_decode($matches[0], true);
                }
            }

            // Se ainda não conseguiu decodificar ou não há tasks, retornar array vazio
            if (json_last_error() !== JSON_ERROR_NONE || !isset($jsonData['tasks']) || !is_array($jsonData['tasks'])) {
                Log::error('Falha ao processar resposta da IA: ' . $response);
                return [];
            }

            // Mapear as tarefas para o formato esperado pelo frontend
            $suggestions = [];
            foreach ($jsonData['tasks'] as $task) {
                if (isset($task['title']) && isset($task['description'])) {
                    $suggestions[] = [
                        'title' => $task['title'],
                        'description' => $task['description']
                    ];
                }
            }

            return $suggestions;
        } catch (Exception $e) {
            Log::error('Erro ao processar resposta da IA: ' . $e->getMessage());
            return [];
        }
    }
}