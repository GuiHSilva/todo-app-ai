<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Services\GenerateTodoSuggestions;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::all();
        return response()->json($todos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Não utilizado em APIs RESTful
        return;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'nullable|boolean',
        ]);

        $todo = Todo::create($validated);
        return response()->json($todo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return response()->json($todo);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        // Não utilizado em APIs RESTful
        return;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'nullable|boolean',
        ]);

        $todo->update($validated);
        return response()->json($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(null, 204);
    }

    /**
     * Get AI suggestions based on user prompt.
     */
    public function aiSuggest(Request $request, GenerateTodoSuggestions $suggestionService)
    {
        // Validação básica
        $validated = $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        try {
            // Obter sugestões usando o serviço
            $suggestions = $suggestionService->getSuggestions($validated['prompt']);

            return response()->json([
                'suggestions' => $suggestions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao processar a solicitação de IA',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
