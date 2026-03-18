<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar gateway e IDs do Mercado Pago à tabela de assinaturas
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('gateway')->default('asaas')->after('plan_id');
            $table->string('mp_preapproval_id')->nullable()->after('asaas_customer_id');
            $table->string('mp_preference_id')->nullable()->after('mp_preapproval_id');
        });

        // Adicionar gateway e ID do Mercado Pago à tabela de módulos de usuário
        Schema::table('user_modules', function (Blueprint $table) {
            $table->string('gateway')->default('asaas')->after('subscription_id');
            $table->string('mp_payment_id')->nullable()->after('asaas_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'mp_preapproval_id', 'mp_preference_id']);
        });

        Schema::table('user_modules', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'mp_payment_id']);
        });
    }
};
