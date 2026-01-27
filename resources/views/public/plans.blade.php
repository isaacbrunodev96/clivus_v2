<!DOCTYPE html>
<html lang="pt-BR" data-theme="carbon-pro" data-color-mode="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planos - CLIVUS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .text-clivus-blue { color: #008eb4; }
        .bg-clivus-blue { background-color: #008eb4; }
        .border-clivus-blue { border-color: #008eb4; }
        
        .hero-title {
            font-size: 3.5rem;
            line-height: 1.1;
            font-weight: 900;
        }
        
        .dashboard-shadow {
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .btn-clivus-outline {
            border: 1.5px solid #008eb4;
            color: #008eb4;
            transition: all 0.3s;
        }
        
        .btn-clivus-outline:hover {
            background-color: #008eb4;
            color: white;
        }

        .check-icon {
            color: #22c55e;
        }

        .feature-dark-bar {
            background-color: #0f172a;
        }
                .video-container {
            position: relative;
            padding-bottom: 56.25%; /* Proporção 16:9 */
            height: 0;
            overflow: hidden;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="antialiased">
   <!-- Top Info Bar & Header (Identical to Image) -->
    <header class="border-b border-gray-100 sticky top-0 bg-white z-50">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/logo.png') }}" alt="Clivus Logo" class="w-10">
                <span class="text-2xl font-bold tracking-tight text-[#0f172a]">Clivus</span>
            </div>

            <!-- Central Info -->
            <div class="hidden md:flex items-center gap-4 text-[11px] font-semibold text-gray-400 uppercase tracking-widest">
                <span>100% Online</span>
                <span class="text-gray-200">|</span>
                <span>Acesso de Qualquer Lugar</span>
            </div>

            <!-- Action Button -->
            <a href="{{ route('login') }}" class="btn-clivus-outline px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider">
                Entrar no Sistema
            </a>
        </div>
    </header>

   <!-- Hero Section with Image Side-by-Side -->
    <section class="pt-16 pb-20 px-6">
        <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="hero-title text-[#0f172a] mb-6">
                    Você Está Misturando as Finanças do <span class="text-clivus-blue">CPF e CNPJ?</span>
                </h1>
                <p class="text-xl text-gray-500 mb-8 max-w-xl leading-relaxed">
                    Isso coloca você em risco com o fisco e impede seu negócio de crescer. O Clivus separa tudo de forma simples e definitiva.
                </p>
                
                <div class="flex flex-wrap gap-4 mb-10"><a href="#plans">
                    <button class="bg-[#008eb4] text-white px-8 py-4 rounded-lg font-bold flex items-center gap-3 hover:bg-[#007696] transition-all">
                        Ver Opções e Começar Agora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                    </a>
                </div>

                <div class="flex flex-wrap gap-8">
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-500">
                        <svg class="w-5 h-5 check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Acesso Imediato
                    </div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-500">
                        <svg class="w-5 h-5 check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Pagamento Único
                    </div>
                    <div class="flex items-center gap-2 text-sm font-semibold text-gray-500">
                        <svg class="w-5 h-5 check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Para Todas as Empresas
                    </div>
                </div>
            </div>

            <!-- Dashboard Image -->
            <div class="relative">
                <div class="dashboard-shadow rounded-2xl overflow-hidden border border-gray-100">
                <img src="{{ asset('assets/dashboard-hero.jpg') }}" alt="Dashboard Clivus" class="w-full h-auto">                    
                </div>
                <div class="text-center mt-6">
                    <p class="font-bold text-[#0f172a]">Dashboard Financeiro</p>
                    <p class="text-sm text-gray-400">Controle total das suas finanças em um só lugar!</p>
                </div>
            </div>
        </div>
    </section>
  <!-- Dark Feature Bar -->
    <div class="feature-dark-bar py-6 px-6">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-center md:justify-around gap-8">
            <div class="flex items-center gap-3 text-white">
                <svg class="w-6 h-6 text-clivus-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span class="font-bold text-xs tracking-widest uppercase">Conformidade Legal</span>
            </div>
            <div class="flex items-center gap-3 text-white">
                <svg class="w-6 h-6 text-clivus-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-bold text-xs tracking-widest uppercase">Segurança Fiscal</span>
            </div>
            <div class="flex items-center gap-3 text-white">
                <svg class="w-6 h-6 text-clivus-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span class="font-bold text-xs tracking-widest uppercase">Crescimento Real</span>
            </div>
        </div>
    </div>


        <!-- Video Section -->
        <section class="py-24 px-4 bg-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Assista ao Vídeo e Descubra Como o Clivus Funciona</h2>
                <p class="text-lg text-gray-500 mb-12">Veja como é simples separar suas finanças PF e PJ em minutos</p>
                
                <div class="video-container">
                    <iframe src="https://www.youtube-nocookie.com/embed/e9u0bGDMhtc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>

                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-center gap-3">
                        <span class="text-blue-500 font-bold">5 min</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Duração do Vídeo</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-center gap-3">
                        <span class="text-blue-500 font-bold">100%</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Conteúdo para evoluir</span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex items-center justify-center gap-3">
                        <span class="text-blue-500 font-bold">0 Bónus</span>
                        <span class="text-xs font-bold text-gray-400 uppercase">Acesso Gratuito</span>
                    </div>
                </div>
  
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs text-green-600 font-medium">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>
            </div>
        </section>
 <section class="py-16 bg-gray-50 border-t border-gray-100">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold text-[#0D2543] mb-6">
                Por Que Você Está Colocando Seu Negócio em Risco
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-12">
                Cada dia que passa com as finanças do CPF e CNPJ misturadas aumenta suas chances de enfrentar:
            </p>

            <!-- Cards de Risco -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Risco 1 -->
                <div class="bg-white border border-red-50 p-8 rounded-xl shadow-sm text-left hover:border-red-200 transition-colors">
                    <div class="text-red-500 mb-4 text-xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-3">Violação da Legislação</h3>
                    <p class="text-sm text-gray-500 mb-4">Misturar finanças PF e PJ viola a separação patrimonial exigida por lei.</p>
                    <p class="text-sm font-bold text-red-600">Você pode estar descumprindo normas legais sem saber.</p>
                </div>

                <!-- Risco 2 -->
                <div class="bg-white border border-red-50 p-8 rounded-xl shadow-sm text-left hover:border-red-200 transition-colors">
                    <div class="text-red-500 mb-4 text-xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-3">Risco Fiscal Iminente</h3>
                    <p class="text-sm text-gray-500 mb-4">Receita Federal pode caracterizar isso como desvio de recursos.</p>
                    <p class="text-sm font-bold text-red-600">Multas, autuações e problemas graves com o fisco.</p>
                </div>

                <!-- Risco 3 -->
                <div class="bg-white border border-red-50 p-8 rounded-xl shadow-sm text-left hover:border-red-200 transition-colors">
                    <div class="text-red-500 mb-4 text-xl">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-3">Barreira ao Crescimento</h3>
                    <p class="text-sm text-gray-500 mb-4">Investidores e bancos não confiam em empresas com finanças misturadas.</p>
                    <p class="text-sm font-bold text-red-600">Impossível escalar e crescer de verdade o negócio.</p>
                </div>
            </div>

            <!-- Rodapé da Secção -->
            <div class="mt-16 text-gray-500 text-sm">
                E você não precisa de contador, consultor ou planilhas complicadas.
            </div>
            
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs text-green-600 font-medium">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>

        </div>
    </section>
        <!-- Apresentação Clivus: Simples. Prático. Objetivo. -->
        <section class="py-24 px-4 bg-gray-50">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-4xl font-black text-gray-900 mb-4">Simples. Prático. Objetivo.</h2>
                <p class="text-gray-500 mb-16 max-w-2xl mx-auto font-medium">Uma ferramenta completa mas descomplicada. Funciona perfeitamente para qualquer tamanho de empresa — de MEI a médio porte.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Cards Funcionalidades -->
                    @php
                        $features = [
                            ['title' => 'Gestão de Tarefas Avançada', 'desc' => 'Organize suas atividades com Kanban interativo, comentários, anexos e subtarefas integradas.'],
                            ['title' => 'Planejamento Financeiro', 'desc' => 'Crie orçamentos, projete resultados e acompanhe metas financeiras para PF e PJ.'],
                            ['title' => 'Relatórios Financeiros', 'desc' => 'Análises avançadas de receitas, despesas, lucro e DRE personalizável em tempo real.'],
                            ['title' => 'Transações PF e PJ', 'desc' => 'Gestão completa de contas a pagar e receber com controle separado de pessoa física e jurídica.'],
                            ['title' => 'Minhas Assinaturas', 'desc' => 'Gerencie assinaturas de planos e módulos adicionais de forma centralizada.'],
                            ['title' => 'Loja de Módulos', 'desc' => 'Compre funcionalidades adicionais sob demanda e expanda seu sistema conforme a necessidade.'],
                            ['title' => 'Parcelamentos Financeiros', 'desc' => 'Parcelamentos nativos em contas a pagar e receber com controle de periodicidade.'],
                            ['title' => 'Integração com Gateways', 'desc' => 'Cobranças recorrentes integradas com Asaas, Stripe e outros gateways de pagamento.']
                        ];
                    @endphp

                    @foreach($features as $feature)
                    <div class="bg-white p-8 rounded-xl border border-gray-100 text-left hover:shadow-lg transition-shadow">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center mb-6">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $feature['desc'] }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="mt-12 bg-clivus rounded-xl p-10 text-white text-left" style="background-color: #008eb4;">
                    <h3 class="text-xl font-bold mb-8 text-center">E muito mais funcionalidades incluídas!</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Compliance Fiscal (IRPF, DASN, PGMEI)</div>
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Reconciliação Bancária (CSV/OFX)</div>
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Controle de Investimentos (CDB, Tesouro, Fundos)</div>
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Calculadora de Pró-labore e Custos CLT</div>
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Dashboard Completo com Visão 360º</div>
                        <div class="flex items-center gap-3 text-sm font-medium"><svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Gestão de Múltiplas Empresas</div>
                    </div>
                </div>
                  
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs text-green-600 font-medium">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>
            </div>
        </section>

        <!-- Nossos Clientes Dizem -->
        <section class="py-24 px-4 bg-white">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-4xl font-black text-gray-900 mb-4">Veja o que Nossos Clientes Dizem</h2>
                <p class="text-gray-500 mb-16 max-w-2xl mx-auto">Mais de 2.000 empreendedores já transformaram suas finanças com o Clivus. Conheça algumas histórias de sucesso reais.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $testimonials = [
                            [
                                'name' => 'Maria Santos',
                                'role' => 'Consultora Contábil',
                                'location' => 'São Paulo, SP',
                                'text' => 'Antes do Clivus, eu misturava tudo. Cartão pessoal para despesas da empresa, era um caos total! Hoje tenho tudo organizado e consigo ver exatamente quanto minha empresa está gerando de lucro real.',
                                'result' => 'Aumento lucro em 35%'
                            ],
                            [
                                'name' => 'João Silva',
                                'role' => 'E-commerce de Roupas',
                                'location' => 'Rio de Janeiro, RJ',
                                'text' => 'O que mais me impressionou foi a facilidade de uso. Os relatórios são fantásticos e me ajudam a tomar decisões muito mais assertivas no meu negócio.',
                                'result' => 'Economizou 15h por mês'
                            ],
                            [
                                'name' => 'Ana Carolina',
                                'role' => 'Agência de Marketing',
                                'location' => 'Florianópolis, SC',
                                'text' => 'Estava com problemas na Receita Federal por causa da mistura das contas. O Clivus não só resolveu isso como me mostrou oportunidades de economia fiscal que eu nem sabia que existiam.',
                                'result' => 'Evitou R$ 12.000 em multas'
                            ],
                            [
                                'name' => 'Carlos Mendes',
                                'role' => 'Dono de Mecânica',
                                'location' => 'Curitiba, PR',
                                'text' => 'Muito simples, agora sei exatamente quanto posso tirar da empresa para minha família sem prejudicar o fluxo de caixa. Minha esposa finalmente parou de reclamar das finanças!',
                                'result' => 'Paz familiar restaurada'
                            ]
                        ];
                    @endphp

                    @foreach($testimonials as $t)
                    <div class="testimonial-card bg-white p-8 rounded-xl text-left border border-gray-100">
                        <div class="flex gap-1 mb-4">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 italic mb-8 text-sm leading-relaxed">"{{ $t['text'] }}"</p>
                        <div class="flex items-center gap-4 border-t border-gray-50 pt-6">
                            <div class="w-12 h-12 bg-clivus rounded-full flex items-center justify-center text-white font-bold" style="background-color: var(--clivus-blue);">
                                {{ substr($t['name'], 0, 1) }}{{ substr(explode(' ', $t['name'])[1], 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">{{ $t['name'] }}</h4>
                                <p class="text-[11px] text-gray-400 uppercase font-bold tracking-wider">{{ $t['role'] }} | {{ $t['location'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 bg-green-50 p-2 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[11px] font-bold text-green-700 uppercase">Resultado: {{ $t['result'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                  
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs text-green-600 font-medium">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>
            </div>
        </section>

        <!-- Call to Action Banner -->
        <div class="px-4 py-12">
            <div class="max-w-6xl mx-auto bg-clivus rounded-2xl p-12 text-center text-white shadow-2xl relative overflow-hidden" style="background-color: #008eb4;">
                <h3 class="text-2xl font-bold mb-8">Junte-se a mais de 2.000 empreendedores satisfeitos!</h3>
                <div class="flex flex-wrap justify-center gap-12 mb-10">
                    <div class="text-center">
                        <div class="text-3xl font-black">98%</div>
                        <div class="text-[10px] uppercase font-bold opacity-80 mt-1 tracking-widest">Taxa de Satisfação</div>
                    </div>
                    <div class="text-center border-l border-white border-opacity-20 pl-12">
                        <div class="text-3xl font-black">4.9/5</div>
                        <div class="text-[10px] uppercase font-bold opacity-80 mt-1 tracking-widest">Avaliação Média</div>
                    </div>
                    <div class="text-center border-l border-white border-opacity-20 pl-12">
                        <div class="text-3xl font-black">30 dias</div>
                        <div class="text-[10px] uppercase font-bold opacity-80 mt-1 tracking-widest">Garantia de Satisfação</div>
                    </div>
                </div>
  
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>

            </div>
            </div>
        </div>

        <!-- Seção de Planos -->
        <section class="py-20 px-4 bg-gray-50" id="plans">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-black mb-4">Escolha a opção Ideal Para o Seu Negócio</h2>
                    <p class="font-bold text-gray-400 uppercase tracking-widest text-sm">Pagamento único • Sem mensalidades • Acesso Imediato</p>
                </div>

                @if($plans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                    <div class="plan-card bg-white p-8 flex flex-col shadow-sm">
                        <div class="text-center mb-8">
                            <h3 class="text-lg font-bold text-clivus mb-2" style="color: var(--clivus-blue);">{{ $plan->name }}</h3>
                            <div class="flex flex-col items-center">
                                <span class="text-4xl font-black text-gray-900">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter mt-1">Pagamento Único</span>
                            </div>
                        </div>

                        <div class="flex-1">
                            <ul class="space-y-3 mb-10">
                                @php
                                    $planAllowedModules = $plan->allowed_modules ?? [];
                                    $includedModules = $allModules->filter(fn($m) => in_array($m->slug, $planAllowedModules));
                                @endphp
                                @foreach($includedModules as $module)
                                <li class="flex items-start gap-3 text-[13px] font-medium text-gray-600">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    {{ $module->name }}
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <a href="{{ route('public.signup', $plan) }}" class="block w-full py-4 rounded-lg bg-[#0f172a] text-white font-bold text-center text-sm transition hover:bg-black uppercase tracking-widest">
                            Começar Agora
                        </a>
                        <p class="text-[10px] text-center text-gray-400 mt-4 leading-tight font-medium uppercase">
                            Parcelamos no cartão • 100% seguro • Suporte incluso
                        </p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- Perguntas Frequentes (FAQ) -->
        <section class="py-24 px-4 bg-white">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-black text-gray-900 mb-4">Perguntas Frequentes</h2>
                    <p class="text-gray-500 font-medium">Esclarecemos as principais dúvidas sobre o Clivus. Se sua pergunta não estiver aqui, nossa equipe está pronta para ajudar!</p>
                </div>

                <div class="space-y-4">
                    @php
                        $faqs = [
                            ['q' => 'O Clivus funciona para qualquer tipo de empresa?', 'a' => 'Sim! O sistema foi desenhado para atender desde MEIs até empresas de médio porte que precisam de uma separação clara entre finanças pessoais e empresariais.'],
                            ['q' => 'Preciso ter conhecimento técnico para usar?', 'a' => 'Não. A interface é intuitiva e focada na simplicidade. Além disso, oferecemos tutoriais e suporte para você começar rápido.'],
                            ['q' => 'E se eu não ficar satisfeito com a compra?', 'a' => 'Oferecemos uma garantia incondicional de 30 dias. Se por qualquer motivo você não se adaptar, devolvemos 100% do seu investimento.'],
                            ['q' => 'O Clivus substitui meu contador?', 'a' => 'Não, ele é uma ferramenta de gestão financeira que facilita o trabalho do seu contador, fornecendo dados organizados e relatórios precisos.'],
                            ['q' => 'Posso usar em mais de uma empresa?', 'a' => 'Sim, dependendo do plano escolhido, você pode gerenciar múltiplas empresas sob o mesmo perfil.'],
                            ['q' => 'Tem custos adicionais ou mensalidades?', 'a' => 'Não! Nosso modelo é de pagamento único. Você paga uma vez e tem acesso vitalício à ferramenta.'],
                            ['q' => 'Posso acessar de qualquer lugar?', 'a' => 'Sim, o Clivus é 100% online e responsivo, funcionando em computadores, tablets e smartphones.']
                        ];
                    @endphp

                    @foreach($faqs as $faq)
                    <div class="faq-item p-6">
                        <button class="flex items-center justify-between w-full text-left focus:outline-none">
                            <span class="font-bold text-gray-900 text-sm">{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="mt-4 text-sm text-gray-500 leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                    @endforeach
                </div>

  
            <div class="mt-8">
                <a href="#plans">
                <button class="bg-[#0097B2] hover:bg-[#007E95] text-white font-bold py-3 px-8 rounded-lg text-md inline-flex items-center transition-all shadow-md uppercase">
                    Ver Opções e Começar
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs text-green-600 font-medium">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-white border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    &copy; {{ date('Y') }} CLIVUS. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>