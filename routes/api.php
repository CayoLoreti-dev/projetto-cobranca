<?php

use App\Http\Controllers\Api\V1\AuthTokenController;
use App\Http\Controllers\Api\V1\ClienteController;
use App\Http\Controllers\Api\V1\CobrancaController;
use App\Http\Controllers\Api\V1\MinhaDemandaController;
use App\Http\Controllers\Api\V1\ParcelaController;
use App\Http\Controllers\Api\V1\RelatorioController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/tokens', [AuthTokenController::class, 'store'])->middleware('throttle:api-token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('throttle:api-read')->group(function () {
            Route::apiResource('clientes', ClienteController::class)->only(['index', 'show']);
            Route::apiResource('cobrancas', CobrancaController::class)->only(['index', 'show']);
            Route::get('minhas-demandas', [MinhaDemandaController::class, 'index']);
        });

        Route::middleware('throttle:api-write')->group(function () {
            Route::apiResource('clientes', ClienteController::class)->only(['store']);
            Route::apiResource('cobrancas', CobrancaController::class)->only(['store']);
            Route::post('parcelas/{parcela}/baixar', [ParcelaController::class, 'baixar']);
        });

        Route::prefix('relatorios')->middleware('throttle:api-report')->group(function () {
            Route::get('resumo', [RelatorioController::class, 'resumo']);
            Route::get('inadimplencia', [RelatorioController::class, 'inadimplencia']);
            Route::get('previsao-recebimento', [RelatorioController::class, 'previsaoRecebimento']);
            Route::get('produtividade', [RelatorioController::class, 'produtividade']);
            Route::get('evolucao-temporal', [RelatorioController::class, 'evolucaoTemporal']);
        });
    });
});
