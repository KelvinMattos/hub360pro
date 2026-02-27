<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // TABELA COMPANIES (base de multitenancy)
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('cnpj')->nullable()->unique();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // TABELA PRODUCTS
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('sku')->nullable()->index();
                $table->string('ean')->nullable()->index();
                $table->decimal('price', 10, 2)->default(0);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->integer('stock_quantity')->default(0);
                $table->string('status')->default('active');
                $table->string('ml_id')->nullable()->index();
                $table->string('ml_category_id')->nullable();
                $table->string('listing_type')->nullable();
                $table->string('condition')->nullable();
                $table->string('permalink')->nullable();
                $table->string('thumbnail')->nullable();
                $table->integer('lead_time')->default(15);
                $table->integer('safety_stock')->default(5);
                $table->json('attributes')->nullable();
                $table->timestamps();
            });
        }

        // TABELA ORDERS (base de pedidos)
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('ml_order_id')->nullable()->unique()->index();
                $table->string('status')->default('pending');
                $table->string('buyer_nickname')->nullable();
                $table->string('buyer_id')->nullable()->index();
                $table->string('buyer_email')->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->decimal('total_paid_amount', 15, 2)->default(0);
                $table->decimal('ml_fee_amount', 10, 2)->default(0);
                $table->decimal('shipping_cost', 10, 2)->default(0);
                $table->decimal('net_shipping_cost', 10, 2)->default(0);
                $table->decimal('cost_price', 10, 2)->default(0);
                $table->decimal('profit', 10, 2)->default(0);
                $table->decimal('contribution_margin', 5, 2)->default(0);
                $table->string('currency_id')->nullable();
                $table->string('ml_account_id')->nullable()->index();
                $table->string('payment_status')->nullable();
                $table->string('shipping_status')->nullable();
                $table->json('items')->nullable();
                $table->json('billing_info')->nullable();
                $table->timestamp('order_date')->nullable();
                $table->timestamps();
            });
        }

        // TABELA CUSTOMERS (clientes)
        if (!Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('ml_buyer_id')->nullable()->unique()->index();
                $table->string('nickname')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->string('origin')->nullable()->default('mercadolivre');
                $table->integer('total_orders')->default(0);
                $table->decimal('total_spent', 15, 2)->default(0);
                $table->timestamp('first_purchase_at')->nullable();
                $table->timestamp('last_purchase_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('companies');
    }
};