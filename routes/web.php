<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MeliIntelligenceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;

// 1. Redirecionamento Inicial
Route::get('/', function () { return redirect()->route('login'); });

// 2. Login
Route::middleware('guest')->group(function () {
    Route::get('/login', function () { return view('auth.login'); })->name('login');
});

// 3. Sistema Protegido (Middleware Higienizado)
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produtos (Master)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/sync', [ProductController::class, 'sync'])->name('products.sync');

    // Clientes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');

    // Pedidos e Etiquetas
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/label', [OrderController::class, 'printLabel'])->name('orders.label');
    Route::post('/orders/{id}/sync', [OrderController::class, 'syncSingle'])->name('sync_single'); // Alias para evitar erro
    Route::post('/notifications/read', [OrderController::class, 'markNotificationsRead'])->name('notifications.read');

    // Inteligência 360 (Hub Avançado)
    Route::get('/meli/war-room', [InventoryController::class, 'warRoom'])->name('meli.war_room');
    Route::get('/inventory/planning', [InventoryController::class, 'planning'])->name('inventory.planning');
    Route::get('/meli/calculator', [InventoryController::class, 'calculator'])->name('meli.calculator');

    // Configurações
    Route::get('/settings/integrations', [SettingsController::class, 'integrations'])->name('settings.integrations'); // Alias
    Route::get('/settings/logs', [SettingsController::class, 'logs'])->name('settings.logs'); // Alias
    
    // Rota de Logout (POST)
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Webhooks
Route::any('/webhook/handle/{platform}', [SettingsController::class, 'handleWebhook'])->name('webhook.handle');

// OAuth
Route::get('/ml/connect', [SettingsController::class, 'redirectToMeli'])->name('ml.connect');
Route::get('/ml/callback', [SettingsController::class, 'handleMeliCallback'])->name('ml.callback');