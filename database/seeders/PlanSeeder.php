<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Plano Mensal',
                'slug' => 'monthly',
                'description' => 'Acesso completo com cobrança mensal',
                'price' => 49.90,
                'billing_cycle' => 'monthly',
                'features' => ['Gestão Financeira', 'Equipe até 5 pessoas', 'Suporte'],
                'allowed_modules' => ['prolabore', 'pricing', 'finance-accounts', 'finance-transactions'],
                'max_accounts' => 5,
                'max_transactions_per_month' => 500,
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Plano Anual',
                'slug' => 'yearly',
                'description' => 'Acesso completo com desconto anual',
                'price' => 499.00,
                'billing_cycle' => 'yearly',
                'features' => ['Gestão Financeira', 'Equipe ilimitada', 'Suporte Prioritário'],
                'allowed_modules' => ['prolabore', 'pricing', 'finance-accounts', 'finance-transactions', 'team-management', 'task-management'],
                'max_accounts' => 20,
                'max_transactions_per_month' => 5000,
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Plano Vitalício',
                'slug' => 'lifetime',
                'description' => 'Acesso para sempre com pagamento único',
                'price' => 1997.00,
                'billing_cycle' => 'lifetime',
                'features' => ['Tudo do Plano Anual', 'Sem mensalidade', 'Novos módulos inclusos'],
                'allowed_modules' => ['prolabore', 'pricing', 'employee-cost', 'compliance', 'finance-accounts', 'finance-transactions', 'finance-planning', 'team-management', 'task-management', 'calendar'],
                'max_accounts' => 100,
                'max_transactions_per_month' => 999999,
                'active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
