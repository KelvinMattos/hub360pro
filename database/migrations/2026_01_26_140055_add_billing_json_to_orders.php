<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Coluna para salvar o JSON completo do Mercado Livre (Segurança de Dados)
            if (!Schema::hasColumn('orders', 'billing_info_json')) {
                $table->longText('billing_info_json')->nullable(); 
            }
            
            // Garantia de campos específicos
            if (!Schema::hasColumn('orders', 'billing_legal_name')) {
                $table->string('billing_legal_name')->nullable(); // Razão Social Oficial
            }
            if (!Schema::hasColumn('orders', 'billing_ie')) {
                $table->string('billing_ie')->nullable(); // Inscrição Estadual
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['billing_info_json', 'billing_legal_name']);
        });
    }
};