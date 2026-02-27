<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsaasWebhookController;

/*
 |--------------------------------------------------------------------------
 | API Routes
 |--------------------------------------------------------------------------
 |
 | Aqui ficam as rotas de integração e externos.
 |
 */

Route::post('/webhook/asaas', [AsaasWebhookController::class , 'handle'])->name('api.webhook.asaas');