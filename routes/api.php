<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;

Route::apiResource('todos', TodoController::class);
Route::post('ai-suggest', [TodoController::class, 'aiSuggest']);