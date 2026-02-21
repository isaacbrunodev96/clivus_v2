@extends('layouts.app')

@section('title', 'Dashboard - CLIVUS')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Visão Geral Financeira -->
    <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold mb-1">Visão Geral Financeira</h2>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Acompanhe rapidamente a saúde financeira do seu negócio</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Saldo de Caixa -->
            <div class="rounded-lg p-5" style="background: linear-gradient(135deg, rgb(var(--primary)), rgb(var(--primary-dark))); color: white;">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium opacity-90">Saldo de Caixa</h3>
                    <svg class="w-6 h-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold mb-2">R$ {{ number_format($totalBalance, 2, ',', '.') }}</p>
                <div class="text-xs opacity-75 space-y-1">
                    <p>PF: R$ {{ number_format($balancePF, 2, ',', '.') }}</p>
                    <p>PJ: R$ {{ number_format($balancePJ, 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- A Receber (30d) -->
            <div class="rounded-lg p-5" style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border));">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium" style="color: rgb(var(--text-secondary));">A Receber (30d)</h3>
                    <svg class="w-6 h-6" style="color: rgb(34, 197, 94);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold mb-2" style="color: rgb(34, 197, 94);">R$ {{ number_format($totalReceivables30d, 2, ',', '.') }}</p>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">{{ $countReceivables30d }} {{ $countReceivables30d == 1 ? 'título pendente' : 'títulos pendentes' }}</p>
            </div>

            <!-- A Pagar (30d) -->
            <div class="rounded-lg p-5" style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border));">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium" style="color: rgb(var(--text-secondary));">A Pagar (30d)</h3>
                    <svg class="w-6 h-6" style="color: rgb(239, 68, 68);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold mb-2" style="color: rgb(239, 68, 68);">R$ {{ number_format($totalPayables30d, 2, ',', '.') }}</p>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">{{ $countPayables30d }} {{ $countPayables30d == 1 ? 'título pendente' : 'títulos pendentes' }}</p>
            </div>

            <!-- Resultado Projetado -->
            <div class="rounded-lg p-5" style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border));">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium" style="color: rgb(var(--text-secondary));">Resultado Projetado</h3>
                    <svg class="w-6 h-6" style="color: rgb(var(--primary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-3xl font-bold mb-2" style="color: {{ $projectedResult >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)' }};">R$ {{ number_format($projectedResult, 2, ',', '.') }}</p>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">Projeção mês atual</p>
            </div>
        </div>
    </div>

    <!-- Grid de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Timeline Financeira - Linha Fina -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">Timeline Financeira</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Receitas e despesas diárias (30 dias)</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                    <div class="w-2.5 h-2.5 rounded-full" style="background-color: rgb(var(--success));"></div>
                        <span class="text-xs" style="color: rgb(var(--text-secondary));">Receitas</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full" style="background-color: rgb(var(--danger));"></div>
                        <span class="text-xs" style="color: rgb(var(--text-secondary));">Despesas</span>
                    </div>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>

        <!-- Saldo Acumulado - Área -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">Saldo Acumulado</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Evolução do saldo (30 dias)</p>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="balanceChart"></canvas>
            </div>
        </div>

        <!-- Comparação Mensal - Barras -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">Comparação Mensal</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Receitas vs Despesas (6 meses)</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded" style="background-color: rgb(var(--success));"></div>
                        <span class="text-xs" style="color: rgb(var(--text-secondary));">Receitas</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded" style="background-color: rgb(var(--danger));"></div>
                        <span class="text-xs" style="color: rgb(var(--text-secondary));">Despesas</span>
                    </div>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- PF vs PJ - Barras Agrupadas -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">PF vs PJ</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Comparação (30 dias)</p>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="pfPjChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grid de Gráficos Circulares -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Métodos de Pagamento - Pizza -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">Métodos de Pagamento</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Distribuição (30 dias)</p>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

        <!-- Distribuição por Conta - Rosca -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold mb-1">Distribuição por Conta</h2>
                    <p class="text-xs" style="color: rgb(var(--text-secondary));">Transações (30 dias)</p>
                </div>
            </div>
            <div style="height: 300px;">
                <canvas id="accountChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Grid de Informações -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Próximas Contas a Pagar -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" style="color: rgb(239, 68, 68);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Próximas Contas a Pagar
                    </h3>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">Vencimentos nos próximos 7 dias</p>
                </div>
                <a href="{{ route('finance.payables.index') }}" class="text-sm font-medium" style="color: rgb(var(--primary));">Ver todas</a>
            </div>
            @if($upcomingPayables->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingPayables->take(5) as $payable)
                <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border));">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $payable->description }}</p>
                        <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">
                            Vence em {{ $payable->due_date->format('d/m/Y') }}
                            @if($payable->due_date->isPast())
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(239, 68, 68, 0.1); color: rgb(239, 68, 68);">Vencida</span>
                            @elseif($payable->due_date->isToday())
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">Hoje</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold" style="color: rgb(239, 68, 68);">R$ {{ number_format($payable->amount, 2, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Nenhuma conta a pagar</p>
            </div>
            @endif
        </div>

        <!-- Próximas Contas a Receber -->
        <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold flex items-center">
                        <svg class="w-5 h-5 mr-2" style="color: rgb(34, 197, 94);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Próximas Contas a Receber
                    </h3>
                    <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">Vencimentos nos próximos 7 dias</p>
                </div>
                <a href="{{ route('finance.receivables.index') }}" class="text-sm font-medium" style="color: rgb(var(--primary));">Ver todas</a>
            </div>
            @if($upcomingReceivables->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingReceivables->take(5) as $receivable)
                <div class="flex items-center justify-between p-3 rounded-lg" style="background-color: rgb(var(--bg-secondary)); border: 1px solid rgb(var(--border));">
                    <div class="flex-1">
                        <p class="font-medium text-sm">{{ $receivable->description }}</p>
                        <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">
                            Vence em {{ $receivable->due_date->format('d/m/Y') }}
                            @if($receivable->due_date->isPast())
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(239, 68, 68, 0.1); color: rgb(239, 68, 68);">Vencida</span>
                            @elseif($receivable->due_date->isToday())
                                <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">Hoje</span>
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold" style="color: rgb(34, 197, 94);">R$ {{ number_format($receivable->amount, 2, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-sm" style="color: rgb(var(--text-secondary));">Nenhuma conta a receber</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Metas Financeiras -->
    <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold">Metas Financeiras</h3>
            <a href="{{ route('finance.planning.index') }}" class="text-sm font-medium" style="color: rgb(var(--primary));">Gerenciar</a>
        </div>
        @if($totalGoals > 0)
        <div class="space-y-4">
            @foreach($goalsProgress as $goal)
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium">{{ $goal['name'] }}</span>
                    <span class="text-sm font-bold" style="color: rgb(var(--primary));">{{ number_format($goal['progress'], 1) }}%</span>
                </div>
                <div class="w-full h-3 rounded-full" style="background-color: rgb(var(--bg-secondary));">
                    <div class="h-3 rounded-full transition-all duration-500" style="background: linear-gradient(90deg, rgb(var(--primary)), rgb(var(--primary-dark))); width: {{ min(100, $goal['progress']) }}%;"></div>
                </div>
                <div class="flex justify-between text-xs mt-1" style="color: rgb(var(--text-secondary));">
                    <span>R$ {{ number_format($goal['current'], 2, ',', '.') }}</span>
                    <span>R$ {{ number_format($goal['target'], 2, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <p class="text-sm" style="color: rgb(var(--text-secondary));">Nenhuma meta cadastrada</p>
        </div>
        @endif
    </div>

    <!-- Transações Recentes -->
    <div class="rounded-xl p-6" style="background-color: rgb(var(--card)); border: 1px solid rgb(var(--border)); box-shadow: var(--shadow);">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold">Transações Recentes</h3>
            <a href="{{ route('finance.transactions.index') }}" class="text-sm font-medium" style="color: rgb(var(--primary));">Ver todas</a>
        </div>
        @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgb(var(--border));">
                        <th class="text-left py-3 px-4 text-sm font-medium" style="color: rgb(var(--text-secondary));">Data</th>
                        <th class="text-left py-3 px-4 text-sm font-medium" style="color: rgb(var(--text-secondary));">Descrição</th>
                        <th class="text-left py-3 px-4 text-sm font-medium" style="color: rgb(var(--text-secondary));">Conta</th>
                        <th class="text-right py-3 px-4 text-sm font-medium" style="color: rgb(var(--text-secondary));">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $transaction)
                    <tr style="border-bottom: 1px solid rgb(var(--border));" class="hover:bg-opacity-50" style="background-color: rgba(var(--primary), 0.02);">
                        <td class="py-3 px-4 text-sm">{{ $transaction->date->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-sm">{{ $transaction->description }}</td>
                        <td class="py-3 px-4 text-sm">{{ $transaction->account->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-sm text-right font-medium" style="color: {{ $transaction->type === 'receita' ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)' }};">
                            {{ $transaction->type === 'receita' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-center py-8" style="color: rgb(var(--text-secondary));">Nenhuma transação recente</p>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Configuração global para linhas finas e tema de charts
    Chart.defaults.borderWidth = 1;
    Chart.defaults.elements.line.borderWidth = 1.2;
    Chart.defaults.elements.line.tension = 0.38;
    Chart.defaults.elements.point.radius = 2;
    Chart.defaults.elements.point.hoverRadius = 4;
    Chart.defaults.elements.bar.borderWidth = 0;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 12;
    Chart.defaults.plugins.legend.labels.font = { size: 11 };
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.titleFont = { size: 12, weight: '600' };
    Chart.defaults.plugins.tooltip.bodyFont = { size: 11 };
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.plugins.tooltip.displayColors = true;
    Chart.defaults.plugins.tooltip.boxPadding = 6;
    // Colors for Chart default text (reads CSS variable)
    try {
        const rootCss = getComputedStyle(document.documentElement);
        Chart.defaults.color = rootCss.getPropertyValue('--text-secondary') || '#9aa8bd';
    } catch (e) {
        Chart.defaults.color = '#9aa8bd';
    }

    // Compute colors from CSS variables so charts follow theme palette (robustly)
    const css = getComputedStyle(document.documentElement);
    const toRgb = (val) => {
        if (!val) return null;
        return `rgb(${val.trim().split(/\s+/).join(',')})`;
    };
    const toRgba = (val, a) => {
        if (!val) return null;
        return `rgba(${val.trim().split(/\s+/).join(',')}, ${a})`;
    };

    const primary = toRgb(css.getPropertyValue('--primary'));
    const primaryDark = toRgb(css.getPropertyValue('--primary-dark'));
    const successColor = toRgb(css.getPropertyValue('--success'));
    const successBg = toRgba(css.getPropertyValue('--success'), 0.06);
    const dangerColor = toRgb(css.getPropertyValue('--danger'));
    const dangerBg = toRgba(css.getPropertyValue('--danger'), 0.06);
    const warningColor = toRgb(css.getPropertyValue('--warning'));
    const infoColor = toRgb(css.getPropertyValue('--info'));
    const infoBg = toRgba(css.getPropertyValue('--info'), 0.08);
    const cardColor = toRgb(css.getPropertyValue('--card'));
    const borderVar = css.getPropertyValue('--border');
    const gridColor = borderVar ? toRgba(borderVar, 0.06) : 'rgba(128,128,128,0.08)';
    const textColor = toRgb(css.getPropertyValue('--text-secondary')) || 'rgb(150,160,175)';

    // Timeline Financeira Chart - Linha Fina
    const timelineCtx = document.getElementById('timelineChart');
    if (timelineCtx) {
        const timelineData = @json($timelineData);
        
        new Chart(timelineCtx, {
            type: 'line',
            data: {
                labels: timelineData.map(d => d.dateShort),
                datasets: [
                    {
                        label: 'Receitas',
                        data: timelineData.map(d => d.revenue),
                        borderColor: successColor || 'rgb(34,197,94)',
                        backgroundColor: successBg || 'rgba(34,197,94,0.06)',
                        borderWidth: 1.2,
                        tension: 0.38,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 3,
                    },
                    {
                        label: 'Despesas',
                        data: timelineData.map(d => d.expense),
                        borderColor: dangerColor || 'rgb(239,68,68)',
                        backgroundColor: dangerBg || 'rgba(239,68,68,0.06)',
                        borderWidth: 1.2,
                        tension: 0.38,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            color: gridColor,
                            lineWidth: 1
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            maxRotation: 0,
                            minRotation: 0
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    // Saldo Acumulado - Área
    const balanceCtx = document.getElementById('balanceChart');
    if (balanceCtx) {
        const balanceData = @json($accumulatedBalanceData);
        
        new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: balanceData.map(d => d.date),
                datasets: [{
                    label: 'Saldo',
                    data: balanceData.map(d => d.balance),
                    borderColor: infoColor || 'rgb(59,130,246)',
                    backgroundColor: infoBg || 'rgba(59,130,246,0.08)',
                    borderWidth: 1.6,
                    tension: 0.38,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Saldo: R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            color: gridColor,
                            lineWidth: 1
                        },
                        border: { display: false }
                    },
                    x: {
                    ticks: {
                            color: textColor,
                            font: { size: 10 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Comparação Mensal - Barras
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        const monthlyData = @json($monthlyData);
        
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.monthShort),
                datasets: [
                    {
                        label: 'Receitas',
                        data: monthlyData.map(d => d.revenue),
                        backgroundColor: toRgba(css.getPropertyValue('--success'), 0.72) || 'rgba(34,197,94,0.7)',
                        borderColor: successColor || 'rgb(34,197,94)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Despesas',
                        data: monthlyData.map(d => d.expense),
                        backgroundColor: toRgba(css.getPropertyValue('--danger'), 0.72) || 'rgba(239,68,68,0.7)',
                        borderColor: dangerColor || 'rgb(239,68,68)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            color: gridColor,
                            lineWidth: 1
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            font: { size: 10 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // PF vs PJ - Barras Agrupadas
    const pfPjCtx = document.getElementById('pfPjChart');
    if (pfPjCtx) {
        const pfPjData = @json($pfPjComparison);
        
        new Chart(pfPjCtx, {
            type: 'bar',
            data: {
                labels: ['Receitas', 'Despesas'],
                datasets: [
                    {
                        label: 'Pessoa Física',
                        data: [pfPjData.pf.receita, pfPjData.pf.despesa],
                        backgroundColor: 'rgba(139, 92, 246, 0.7)',
                        borderColor: 'rgb(139, 92, 246)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Pessoa Jurídica',
                        data: [pfPjData.pj.receita, pfPjData.pj.despesa],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: { size: 11 },
                            padding: 10,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            font: { size: 10 },
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            color: gridColor,
                            lineWidth: 1
                        },
                        border: { display: false }
                    },
                    x: {
                        ticks: {
                            color: textColor,
                            font: { size: 10 }
                        },
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // Métodos de Pagamento - Pizza
    const paymentMethodCtx = document.getElementById('paymentMethodChart');
    if (paymentMethodCtx) {
        const paymentMethodData = @json($paymentMethodData);
        const colors = [
            'rgba(34, 197, 94, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ];
        
        if (paymentMethodData && paymentMethodData.length > 0) {
            new Chart(paymentMethodCtx, {
            type: 'pie',
            data: {
                labels: paymentMethodData.map(d => d.method),
                datasets: [{
                    data: paymentMethodData.map(d => d.total),
                    backgroundColor: colors.slice(0, paymentMethodData.length),
                    borderWidth: 2,
                    borderColor: 'rgb(var(--card))',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: textColor,
                            font: { size: 11 },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
            });
        } else {
            paymentMethodCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-sm" style="color: rgb(var(--text-secondary));">Nenhum dado disponível</div>';
        }
    }

    // Distribuição por Conta - Rosca
    const accountCtx = document.getElementById('accountChart');
    if (accountCtx) {
        const accountData = @json($accountDistributionData);
        const colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ];
        
        if (accountData && accountData.length > 0) {
            new Chart(accountCtx, {
            type: 'doughnut',
            data: {
                labels: accountData.map(d => d.account),
                datasets: [{
                    data: accountData.map(d => d.total),
                    backgroundColor: colors.slice(0, accountData.length),
                    borderWidth: 2,
                    borderColor: 'rgb(var(--card))',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: textColor,
                            font: { size: 11 },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
            });
        } else {
            accountCtx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-sm" style="color: rgb(var(--text-secondary));">Nenhum dado disponível</div>';
        }
    }

    // Sistema de verificação automática de pagamentos pendentes
    (function() {
        let checkInterval = null;
        let checkCount = 0;
        const maxChecks = 60;
        const checkIntervalTime = 5000;
        
        function showNotification(message, type = 'success') {
            const existingNotification = document.getElementById('payment-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            const notification = document.createElement('div');
            notification.id = 'payment-notification';
            notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300';
            notification.style.cssText = type === 'success' 
                ? 'background-color: rgba(34, 197, 94, 0.95); color: white; border: 1px solid rgb(22, 163, 74);'
                : 'background-color: rgba(59, 130, 246, 0.95); color: white; border: 1px solid rgb(37, 99, 235);';
            
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 10);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 10000);
        }
        
        function checkPendingPayments() {
            checkCount++;
            
            if (checkCount > maxChecks) {
                if (checkInterval) {
                    clearInterval(checkInterval);
                    checkInterval = null;
                }
                return;
            }
            
            fetch('{{ route("payment.pending.check") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.updated && data.message) {
                    showNotification(data.message, 'success');
                    
                    if (checkInterval) {
                        clearInterval(checkInterval);
                        checkInterval = null;
                    }
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else if (!data.has_pending) {
                    if (checkInterval) {
                        clearInterval(checkInterval);
                        checkInterval = null;
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao verificar pagamentos pendentes:', error);
            });
        }
        
        function startPaymentCheck() {
            if (checkInterval) {
                return;
            }
            
            fetch('{{ route("payment.pending.check") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_pending) {
                    checkInterval = setInterval(checkPendingPayments, checkIntervalTime);
                    checkPendingPayments();
                }
            })
            .catch(error => {
                console.error('Erro ao verificar pagamentos pendentes:', error);
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', startPaymentCheck);
        } else {
            startPaymentCheck();
        }
        
        window.addEventListener('focus', function() {
            if (!checkInterval) {
                startPaymentCheck();
            }
        });
        
        window.addEventListener('beforeunload', function() {
            if (checkInterval) {
                clearInterval(checkInterval);
            }
        });
    })();
</script>
@endpush
@endsection
