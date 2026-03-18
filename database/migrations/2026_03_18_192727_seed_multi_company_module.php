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
        \DB::table('modules')->insert([
            'name' => 'Multi-empresa',
            'slug' => 'multi-company',
            'description' => 'Permite cadastrar e gerenciar múltiplas empresas em uma única conta.',
            'category' => 'Management',
            'price' => 49.90,
            'billing_cycle' => 'monthly',
            'active' => true,
            'sort_order' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('modules')->where('slug', 'multi-company')->delete();
    }
};
