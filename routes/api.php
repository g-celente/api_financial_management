<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\InsightController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/signUp', [AuthController::class, 'register']);
Route::post('/signIn', [AuthController::class, 'signIn']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::middleware(['auth:api', 'retrieve.user'])->group(function () {

    //Rotas para Gerenciar movimentações
    Route::get('/financial', [FinancialController::class, 'index']);
    Route::post('/financial', [FinancialController::class, 'store']);
    Route::put('/financial/{id}',[FinancialController::class, 'update']);
    Route::delete('/financial/{id}', [FinancialController::class, 'destroy']);
    Route::get('/financial/entradas', [FinancialController::class, 'getEntradasByUser']);
    Route::get('/financial/saidas', [FinancialController::class, 'getSaidasByUser']);
    Route::get('/financial/{movimentacao_id}', [FinancialController::class, 'getMovimentacaoById']);

    //Rotas para Gerenciar Categorias
    Route::get('/categories', [CategoriaController::class, 'index']);
    Route::post('/categories', [CategoriaController::class, 'store']);
    Route::delete('/categories/{id}', [CategoriaController::class, 'destroy']);

    //Rotas para Gerenciar Dashboard
    Route::get('/dashboard', [InsightController::class, 'index']);
});