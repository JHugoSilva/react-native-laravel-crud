<?php

use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('todos')->group(function(){
    Route::get('/getTodos', [TodoController::class, 'index']);
    Route::post('/createTodos', [TodoController::class, 'store']);
    Route::get('/getTodo/{todo}', [TodoController::class, 'show']);
    Route::put('/updatedTodo/{todo}', [TodoController::class, 'update']);
    Route::delete('/deleteTodo/{todo}', [TodoController::class, 'destroy']);
});
