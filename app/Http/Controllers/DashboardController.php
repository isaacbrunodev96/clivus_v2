<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Contact;
use App\Models\FinancialGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Se for super admin, redirecionar para dashboard admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Verificar automaticamente pagamentos pendentes (módulos e assinaturas)
        $this->checkPendingPayments($user);

        // Verificar se veio de um pagamento bem-sucedido
        if ($request->has('payment') && $request->payment === 'success') {
            // Verificar se é pagamento de módulo
            if ($request->has('module')) {
                $moduleId = $request->query('module');
                $userModule = \App\Models\UserModule::where('user_id', $user->id)
                    ->where('module_id', $moduleId)
                    ->where('status', 'active')
                    ->first();
                
                if ($userModule) {
                    session()->flash('success', 'Módulo ativado com sucesso!');
                } else {
                    // Tentar verificar se o pagamento foi confirmado mas o módulo ainda não foi ativado
                    $userModule = \App\Models\UserModule::where('user_id', $user->id)
                        ->where('module_id', $moduleId)
                        ->where('status', 'inactive')
                        ->first();
                    
                    if ($userModule && $userModule->asaas_payment_id) {
                        try {
                            $payment = app(\App\Services\AsaasService::class)->getPayment($userModule->asaas_payment_id);
                            if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                                $userModule->update(['status' => 'active']);
                                session()->flash('success', 'Módulo ativado com sucesso!');
                            } else {
                                session()->flash('info', 'Aguardando confirmação do pagamento. O módulo será ativado automaticamente quando o pagamento for confirmado.');
                            }
                        } catch (\Exception $e) {
                            session()->flash('info', 'Aguardando confirmação do pagamento. O módulo será ativado automaticamente quando o pagamento for confirmado.');
                        }
                    }
                }
            } else {
                // Verificar se a assinatura foi ativada
                $subscription = $user->activeSubscription();
                if ($subscription && $subscription->status === 'active') {
                    session()->flash('success', 'Assinatura ativada com sucesso! Bem-vindo ao CLIVUS!');
                } else {
                    // Tentar verificar se o pagamento foi confirmado mas a assinatura ainda não foi ativada
                    $pendingSubscription = $user->subscriptions()
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                    
                    if ($pendingSubscription) {
                        try {
                            $payment = app(\App\Services\AsaasService::class)->getSubscriptionPayments($pendingSubscription->asaas_subscription_id);
                            if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                                $pendingSubscription->update(['status' => 'active']);
                                session()->flash('success', 'Assinatura ativada com sucesso! Bem-vindo ao CLIVUS!');
                            } else {
                                session()->flash('info', 'Aguardando confirmação do pagamento. Sua assinatura será ativada automaticamente quando o pagamento for confirmado.');
                            }
                        } catch (\Exception $e) {
                            session()->flash('info', 'Aguardando confirmação do pagamento. Sua assinatura será ativada automaticamente quando o pagamento for confirmado.');
                        }
                    }
                }
            }
        }

        // Estatísticas financeiras
        $accounts = Account::where('user_id', $user->id)->where('active', true)->get();
        $totalAccounts = $accounts->count();
        $totalBalance = $accounts->sum('balance');

        // Separar saldo por PF e PJ baseado nas contas a pagar/receber
        // Calcular valores pendentes por tipo
        $payablesPF = Payable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('type', 'Pessoa Física (PF)')
            ->sum('amount');
        $payablesPJ = Payable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('type', 'Pessoa Jurídica (PJ)')
            ->sum('amount');
        
        $receivablesPF = Receivable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('type', 'Pessoa Física (PF)')
            ->sum('amount');
        $receivablesPJ = Receivable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('type', 'Pessoa Jurídica (PJ)')
            ->sum('amount');
        
        // Calcular saldo líquido por tipo (saldo das contas + receber - pagar)
        // Distribuir saldo total proporcionalmente baseado nas operações
        $totalOperationsPF = $receivablesPF + $payablesPF;
        $totalOperationsPJ = $receivablesPJ + $payablesPJ;
        $totalOperations = $totalOperationsPF + $totalOperationsPJ;
        
        if ($totalOperations > 0) {
            $pfRatio = $totalOperationsPF / $totalOperations;
            $pjRatio = $totalOperationsPJ / $totalOperations;
        } else {
            $pfRatio = 0.5;
            $pjRatio = 0.5;
        }
        
        $balancePF = $totalBalance * $pfRatio;
        $balancePJ = $totalBalance * $pjRatio;

        // Transações do mês atual
        $currentMonth = now()->startOfMonth();
        $transactions = Transaction::where('user_id', $user->id)
            ->where('date', '>=', $currentMonth)
            ->get();
        
        $monthlyRevenue = $transactions->where('type', 'receita')->sum('amount');
        $monthlyExpenses = $transactions->where('type', 'despesa')->sum('amount');
        $monthlyBalance = $monthlyRevenue - $monthlyExpenses;

        // Contas a pagar e receber
        $payables = Payable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();
        $totalPayables = $payables->sum('amount');
        $overduePayables = $payables->where('due_date', '<', now())->sum('amount');

        $receivables = Receivable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();
        $totalReceivables = $receivables->sum('amount');
        $overdueReceivables = $receivables->where('due_date', '<', now())->sum('amount');

        // Próximas contas a pagar (7 dias)
        $next7Days = now()->addDays(7);
        $upcomingPayables = Payable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), $next7Days])
            ->orderBy('due_date', 'asc')
            ->get();

        // Próximas contas a receber (7 dias)
        $upcomingReceivables = Receivable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), $next7Days])
            ->orderBy('due_date', 'asc')
            ->get();

        // A Receber e A Pagar (30 dias)
        $next30Days = now()->addDays(30);
        $receivables30d = Receivable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), $next30Days])
            ->get();
        $totalReceivables30d = $receivables30d->sum('amount');
        $countReceivables30d = $receivables30d->count();

        $payables30d = Payable::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereBetween('due_date', [now(), $next30Days])
            ->get();
        $totalPayables30d = $payables30d->sum('amount');
        $countPayables30d = $payables30d->count();

        // Resultado Projetado (mês atual)
        $projectedResult = $monthlyRevenue - $monthlyExpenses + $totalReceivables30d - $totalPayables30d;

        // Contatos
        $totalContacts = Contact::where('user_id', $user->id)->count();

        // Metas financeiras
        $goals = FinancialGoal::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();
        $totalGoals = $goals->count();
        $goalsProgress = $goals->map(function($goal) {
            $progress = $goal->target_value > 0 
                ? ($goal->current_value / $goal->target_value) * 100 
                : 0;
            return [
                'name' => $goal->name,
                'progress' => min(100, max(0, $progress)),
                'current' => $goal->current_value,
                'target' => $goal->target_value,
            ];
        });

        // Transações recentes
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('account')
            ->latest('date')
            ->take(10)
            ->get();

        // Timeline Financeira - últimos 30 dias por dia
        $timelineStart = now()->subDays(30);
        $timelineEnd = now();
        $timelineData = [];
        $accumulatedBalance = $totalBalance;
        
        for ($date = $timelineStart->copy(); $date->lte($timelineEnd); $date->addDay()) {
            $dayRevenue = Transaction::where('user_id', $user->id)
                ->where('type', 'receita')
                ->whereDate('date', $date->format('Y-m-d'))
                ->sum('amount');
            
            $dayExpense = Transaction::where('user_id', $user->id)
                ->where('type', 'despesa')
                ->whereDate('date', $date->format('Y-m-d'))
                ->sum('amount');
            
            $dayBalance = $dayRevenue - $dayExpense;
            $accumulatedBalance += $dayBalance;
            
            $timelineData[] = [
                'date' => $date->format('d/m/Y'),
                'dateShort' => $date->format('d/m'),
                'revenue' => $dayRevenue,
                'expense' => $dayExpense,
                'balance' => $accumulatedBalance,
            ];
        }

        // Gráfico de receitas vs despesas (últimos 6 meses)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Transaction::where('user_id', $user->id)
                ->where('type', 'receita')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $expense = Transaction::where('user_id', $user->id)
                ->where('type', 'despesa')
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            
            $monthlyData[] = [
                'month' => $month->format('M/Y'),
                'monthShort' => $month->format('M'),
                'revenue' => $revenue,
                'expense' => $expense,
            ];
        }

        // Distribuição por método de pagamento (últimos 30 dias)
        $paymentMethodData = Transaction::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(30))
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->map(function($item) {
                return [
                    'method' => $item->payment_method ?: 'Não informado',
                    'total' => (float) $item->total
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->values();

        // Distribuição por conta (últimos 30 dias)
        $accountDistributionData = Transaction::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays(30))
            ->with('account')
            ->get()
            ->groupBy('account_id')
            ->map(function($transactions, $accountId) {
                $account = $transactions->first()->account;
                return [
                    'account' => $account ? $account->name : 'Sem conta',
                    'total' => $transactions->sum('amount')
                ];
            })
            ->filter(fn($item) => $item['total'] > 0)
            ->values();

        // Dados de saldo acumulado para gráfico de área
        $accumulatedBalanceData = [];
        $runningBalance = $totalBalance;
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayTransactions = Transaction::where('user_id', $user->id)
                ->whereDate('date', $date->format('Y-m-d'))
                ->get();
            
            $dayChange = $dayTransactions->sum(function($t) {
                return $t->type === 'receita' ? $t->amount : -$t->amount;
            });
            
            $runningBalance += $dayChange;
            
            $accumulatedBalanceData[] = [
                'date' => $date->format('d/m'),
                'balance' => $runningBalance
            ];
        }

        // Comparação PF vs PJ (últimos 30 dias) - usando Payables e Receivables
        $pfPjComparison = [
            'pf' => [
                'receita' => Receivable::where('user_id', $user->id)
                    ->where('type', 'Pessoa Física (PF)')
                    ->where('due_date', '>=', now()->subDays(30))
                    ->where('status', 'paid')
                    ->sum('amount'),
                'despesa' => Payable::where('user_id', $user->id)
                    ->where('type', 'Pessoa Física (PF)')
                    ->where('due_date', '>=', now()->subDays(30))
                    ->where('status', 'paid')
                    ->sum('amount'),
            ],
            'pj' => [
                'receita' => Receivable::where('user_id', $user->id)
                    ->where('type', 'Pessoa Jurídica (PJ)')
                    ->where('due_date', '>=', now()->subDays(30))
                    ->where('status', 'paid')
                    ->sum('amount'),
                'despesa' => Payable::where('user_id', $user->id)
                    ->where('type', 'Pessoa Jurídica (PJ)')
                    ->where('due_date', '>=', now()->subDays(30))
                    ->where('status', 'paid')
                    ->sum('amount'),
            ]
        ];

        // Assinatura
        $subscription = $user->activeSubscription();
        $plan = $subscription?->plan;

        return view('dashboard.index', compact(
            'totalAccounts',
            'totalBalance',
            'balancePF',
            'balancePJ',
            'monthlyRevenue',
            'monthlyExpenses',
            'monthlyBalance',
            'totalPayables',
            'overduePayables',
            'totalReceivables',
            'overdueReceivables',
            'upcomingPayables',
            'upcomingReceivables',
            'totalReceivables30d',
            'countReceivables30d',
            'totalPayables30d',
            'countPayables30d',
            'projectedResult',
            'totalContacts',
            'totalGoals',
            'goalsProgress',
            'recentTransactions',
            'monthlyData',
            'timelineData',
            'paymentMethodData',
            'accountDistributionData',
            'accumulatedBalanceData',
            'pfPjComparison',
            'subscription',
            'plan'
        ));
    }

    /**
     * Verificar e ativar pagamentos pendentes automaticamente
     */
    private function checkPendingPayments($user)
    {
        // Verificar módulos pendentes
        $pendingModules = \App\Models\UserModule::where('user_id', $user->id)
            ->where('status', 'inactive')
            ->whereNotNull('asaas_payment_id')
            ->get();

        foreach ($pendingModules as $userModule) {
            try {
                $payment = app(\App\Services\AsaasService::class)->getPayment($userModule->asaas_payment_id);
                if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                    $userModule->update(['status' => 'active']);
                    Log::info('Módulo ativado automaticamente no dashboard', [
                        'user_module_id' => $userModule->id,
                        'module_id' => $userModule->module_id,
                        'payment_id' => $userModule->asaas_payment_id
                    ]);
                }
            } catch (\Exception $e) {
                    Log::warning('Erro ao verificar pagamento de módulo no dashboard', [
                    'user_module_id' => $userModule->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Verificar assinaturas pendentes
        $pendingSubscriptions = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereNotNull('asaas_subscription_id')
            ->get();

        foreach ($pendingSubscriptions as $subscription) {
            try {
                $subscriptionData = app(\App\Services\AsaasService::class)->getSubscription($subscription->asaas_subscription_id);
                if ($subscriptionData && isset($subscriptionData['status']) && $subscriptionData['status'] === 'ACTIVE') {
                    $subscription->update(['status' => 'active']);
                    Log::info('Assinatura ativada automaticamente no dashboard', [
                        'subscription_id' => $subscription->id,
                        'asaas_subscription_id' => $subscription->asaas_subscription_id
                    ]);
                } else {
                    // Tentar verificar pelos pagamentos da assinatura
                    $payment = app(\App\Services\AsaasService::class)->getSubscriptionPayments($subscription->asaas_subscription_id);
                    if ($payment && isset($payment['status']) && $payment['status'] === 'CONFIRMED') {
                        $subscription->update(['status' => 'active']);
                        Log::info('Assinatura ativada automaticamente via pagamento no dashboard', [
                            'subscription_id' => $subscription->id,
                            'asaas_subscription_id' => $subscription->asaas_subscription_id
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Erro ao verificar assinatura no dashboard', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
