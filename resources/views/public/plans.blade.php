<!DOCTYPE html>
<html lang="pt-BR" data-theme="carbon-pro" data-color-mode="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planos - CLIVUS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{
            --bg-900: #071023;
            --bg-800: #0b1220;
            --glass: rgba(255,255,255,0.04);
            --accent: #8b5cf6;
            --accent-2: #00b4cc;
            --muted: #94a3b8;
        }
        html,body{height:100%}
        body{
            font-family: 'Inter', sans-serif;
            background: radial-gradient(1200px 600px at 10% 10%, rgba(139,92,246,0.08), transparent),
                        linear-gradient(180deg, var(--bg-900), var(--bg-800));
            color: #e6eef8;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }
        #hero-particles{position:absolute;inset:0;z-index:0;pointer-events:none}
        .site-header{position:sticky;top:0;z-index:40;background:linear-gradient(180deg, rgba(2,6,23,0.6), rgba(2,6,23,0.3));backdrop-filter: blur(6px);border-bottom:1px solid rgba(255,255,255,0.03)}
        .logo-text{font-weight:800;letter-spacing:0.6px;color:var(--accent)}
        .hero-title{font-size:2.6rem;line-height:1.02;font-weight:900}
        .muted{color:var(--muted)}
        .plan-card{
            background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
            border: 1px solid rgba(255,255,255,0.04);
            box-shadow: 0 10px 30px rgba(2,6,23,0.6);
            border-radius: 1rem;
            transition: transform .45s cubic-bezier(.2,.9,.35,1), box-shadow .35s;
            transform: translateY(0) rotate(0.01deg);
        }
        .plan-card:hover{
            transform: translateY(-14px) scale(1.02);
            box-shadow: 0 30px 60px rgba(3,7,25,0.7);
        }
        .price{
            font-weight:900;font-size:2.25rem;
            background: linear-gradient(90deg,var(--accent),var(--accent-2));
            -webkit-background-clip:text;background-clip:text;color:transparent;
        }
        .badge-reco{
            background: linear-gradient(90deg,#1f2937, rgba(255,255,255,0.03));
            border:1px solid rgba(255,255,255,0.05);
            color:var(--accent);
            font-weight:700;
            padding:4px 10px;border-radius:999px;font-size:12px;
        }
        .cta-btn{
            background: linear-gradient(90deg,var(--accent),var(--accent-2));
            color:white;padding:12px;border-radius:12px;font-weight:800;
            box-shadow: 0 8px 30px rgba(139,92,246,0.18);
            transition: transform .18s;
        }
        .cta-btn:active{transform:translateY(1px) scale(.995)}
        @keyframes floatY{
            0%{transform:translateY(0)}
            50%{transform:translateY(-8px)}
            100%{transform:translateY(0)}
        }
        .float-slow{animation: floatY 6s ease-in-out infinite}
        @media (min-width:1024px){ .hero-title{font-size:3.6rem} }
        @media (prefers-reduced-motion: reduce){
            .plan-card, .cta-btn, .float-slow{animation:none;transition:none}
        }
    </style>
</head>
<body class="antialiased relative">
    <div id="hero-particles" style="position:absolute;inset:0;z-index:0;pointer-events:none"></div>
   <!-- Top Info Bar & Header (Dark, sticky) -->
    <header class="site-header">
        <div class="max-w-[1400px] mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/logo.png') }}" alt="Clivus Logo" class="w-10">
                <span class="logo-text text-lg">CLIVUS</span>
            </div>

            <div class="hidden md:flex items-center gap-6 muted text-xs uppercase font-semibold tracking-wider">
                <span>100% Online</span>
                <span>|</span>
                <span>Suporte Premium</span>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-white/6 muted hover:bg-white/3 transition">Entrar</a>
                <a href="#plans" class="cta-btn hidden sm:inline-flex items-center gap-3">Ver Planos</a>
            </div>
        </div>
    </header>

   <!-- Hero Section with Image Side-by-Side -->
    <section class="pt-16 pb-20 px-6">
        <div class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="hero-title mb-6" style="color:#ffffff;text-shadow:0 10px 30px rgba(2,6,23,0.6);">
                    Você Está Misturando as Finanças do <span style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); -webkit-background-clip:text; background-clip:text; color:transparent;font-weight:900">CPF &amp; CNPJ?</span>
                </h1>
                <p class="text-xl muted mb-8 max-w-xl leading-relaxed">
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


        <!-- Vídeo - Dark Showcase -->
        <section class="py-20 px-4" style="background: linear-gradient(180deg, rgba(255,255,255,0.01), transparent);">
            <div class="max-w-5xl mx-auto text-center relative z-10">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4" style="color: #e6eef8;">Assista ao Vídeo — Entenda o Valor</h2>
                <p class="muted text-lg mb-8">Em poucos minutos você verá porque o Clivus é a solução premium para separar CPF e CNPJ.</p>

                <div class="relative mx-auto mb-8" style="max-width:900px;">
                    <div id="video-card" class="rounded-2xl overflow-hidden" style="background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.04); box-shadow:0 20px 60px rgba(2,6,23,0.6);">
                        <div style="position:relative;padding-top:56.25%;">
                            <iframe id="vsl-iframe" src="https://www.youtube-nocookie.com/embed/e9u0bGDMhtc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position:absolute;inset:0;width:100%;height:100%;border:0;"></iframe>
                        </div>
                    </div>
                    <!-- decorative floating badge -->
                    <div class="absolute -bottom-6 left-6 float-slow" style="background:rgba(255,255,255,0.03);padding:10px 14px;border-radius:12px;border:1px solid rgba(255,255,255,0.04);">
                        <span class="font-bold" style="color:var(--accent)">Demonstração Rápida</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                        <div class="font-bold" style="color:#fff">5 min</div>
                        <div class="muted text-xs uppercase">Duração</div>
                    </div>
                    <div class="p-4 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                        <div class="font-bold" style="color:#fff">100%</div>
                        <div class="muted text-xs uppercase">Conteúdo Essencial</div>
                    </div>
                    <div class="p-4 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                        <div class="font-bold" style="color:#fff">Sem Custos</div>
                        <div class="muted text-xs uppercase">Acesso Gratuito</div>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="#plans">
                    <button class="cta-btn">Ver Planos e Começar</button>
                    </a>
                </div>
            </div>
        </section>
    <section class="py-16" style="border-top:1px solid rgba(255,255,255,0.03);">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-6" style="color:#e6eef8;">
                Evite Riscos Fiscais e Cresça com Segurança
            </h2>
            <p class="muted max-w-2xl mx-auto mb-12">
                Manter finanças PF e PJ separadas reduz exposição a autuações, multas e bloqueios de crescimento.
            </p>

            <!-- Cards de Risco (dark) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-8 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                    <div class="text-red-400 mb-4 text-xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="font-bold mb-3" style="color:#fff">Violação da Legislação</h3>
                    <p class="muted text-sm mb-4">Misturar contas pode inviabilizar sua conformidade fiscal.</p>
                    <p class="text-sm font-bold" style="color:#ff8a80">Risco de autuações e multas.</p>
                </div>

                <div class="p-8 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                    <div class="text-red-400 mb-4 text-xl"><i class="fa-solid fa-bolt"></i></div>
                    <h3 class="font-bold mb-3" style="color:#fff">Risco Fiscal Iminente</h3>
                    <p class="muted text-sm mb-4">Falhas de gestão abrem portas para autuações e problemas com a Receita.</p>
                    <p class="text-sm font-bold" style="color:#ff8a80">Impacto direto no caixa.</p>
                </div>

                <div class="p-8 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                    <div class="text-red-400 mb-4 text-xl"><i class="fa-solid fa-chart-line"></i></div>
                    <h3 class="font-bold mb-3" style="color:#fff">Barreira ao Crescimento</h3>
                    <p class="muted text-sm mb-4">Investidores e bancos exigem transparência financeira.</p>
                    <p class="text-sm font-bold" style="color:#ff8a80">Menor acesso a crédito e oportunidades.</p>
                </div>
            </div>

            <div class="mt-12 muted">
                <p>Não precisa complicar — o Clivus automatiza a separação e mantém tudo em conformidade.</p>
            </div>

            <div class="mt-8">
                <a href="#plans">
                    <button class="cta-btn">Ver Opções e Começar</button>
                </a>
            </div>
        </div>
    </section>
        <!-- Apresentação Clivus: Simples. Prático. Objetivo. -->
        <section class="py-24 px-4">
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
                            ['title' => 'Integração com Gateways', 'desc' => 'Cobranças recorrentes integradas com Asaas e outros gateways de pagamento.']
                        ];
                    @endphp

                    @foreach($features as $feature)
                    <div class="p-8 rounded-xl" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-6" style="background:rgba(255,255,255,0.03);">
                            <svg class="w-5 h-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="font-bold mb-3" style="color:#fff">{{ $feature['title'] }}</h3>
                        <p class="text-sm muted leading-relaxed">{{ $feature['desc'] }}</p>
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
        <section class="py-24 px-4">
            <div class="max-w-6xl mx-auto text-center">
                <h2 class="text-4xl font-black mb-4" style="color:#e6eef8;">Veja o que Nossos Clientes Dizem</h2>
                <p class="muted mb-16 max-w-2xl mx-auto">Mais de 2.000 empreendedores já transformaram suas finanças com o Clivus. Conheça algumas histórias de sucesso reais.</p>
                
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
                    <div class="testimonial-card p-8 rounded-xl text-left" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);">
                        <div class="flex gap-1 mb-4">
                            @for($i=0; $i<5; $i++)
                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="italic mb-8 text-sm leading-relaxed" style="color:#dbeafe">"{{ $t['text'] }}"</p>
                        <div class="flex items-center gap-4 border-t border-white/5 pt-6">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">
                                {{ substr($t['name'], 0, 1) }}{{ substr(explode(' ', $t['name'])[1], 0, 1) }}
                            </div>
                            <div class="">
                                <h4 class="font-bold text-sm" style="color:#fff">{{ $t['name'] }}</h4>
                                <p class="text-[11px] muted uppercase font-bold tracking-wider">{{ $t['role'] }} | {{ $t['location'] }}</p>
                            </div>
                        </div>
                        <div class="mt-4 p-2 rounded-lg flex items-center gap-2" style="background:rgba(16,185,129,0.06);">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-[11px] font-bold" style="color:#bbf7d0">Resultado: {{ $t['result'] }}</span>
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

        <!-- Seção de Planos (Dark Premium) -->
        <section id="plans" class="py-12 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <p class="badge-reco inline-block mb-4">Escolha seu acesso vitalício</p>
                    <h2 class="text-3xl font-extrabold mb-2">Planos Premium — Pagamento Único</h2>
                    <p class="muted">Preços atualizados automaticamente. Sem mensalidades. Segurança e suporte incluídos.</p>
                </div>

                @if($plans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                    <div class="plan-card p-8 flex flex-col" role="article" aria-labelledby="plan-{{ $plan->id }}">
                        <div class="flex items-center justify-between mb-6">
                            <h3 id="plan-{{ $plan->id }}" class="text-lg font-bold">{{ $plan->name }}</h3>
                            @if(optional($plan)->recommended)
                                <span class="badge-reco">Recomendado</span>
                            @endif
                        </div>

                        <div class="mb-6">
                            <div class="price">R$ {{ number_format($plan->price, 2, ',', '.') }}</div>
                            <div class="muted text-xs uppercase mt-1">Pagamento Único • Acesso Vitalício</div>
                        </div>

                        <div class="flex-1 mb-6">
                            <ul class="space-y-3 muted text-sm">
                                @php
                                    $planAllowedModules = $plan->allowed_modules ?? [];
                                    $includedModules = $allModules->filter(fn($m) => in_array($m->slug, $planAllowedModules));
                                @endphp
                                @foreach($includedModules as $module)
                                <li class="flex items-start gap-3">
                                    <svg class="w-4 h-4 text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    <span>{{ $module->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('public.signup', $plan) }}" class="w-full inline-flex justify-center cta-btn py-3">Começar Agora</a>
                            <p class="text-center muted text-xs mt-3">Parcelamos no cartão • 100% seguro • Suporte incluso</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- Perguntas Frequentes (FAQ) - Dark -->
        <section class="py-24 px-4" style="background:linear-gradient(180deg, rgba(255,255,255,0.01), transparent);">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-black mb-4" style="color:#e6eef8;">Perguntas Frequentes</h2>
                    <p class="muted font-medium">Esclarecemos as principais dúvidas sobre o Clivus. Se sua pergunta não estiver aqui, nossa equipe está pronta para ajudar!</p>
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
                    <div class="faq-item p-6" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);border-radius:12px;">
                        <button class="flex items-center justify-between w-full text-left focus:outline-none">
                            <span class="font-bold text-sm" style="color:#e6eef8;">{{ $faq['q'] }}</span>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="mt-4 text-sm muted leading-relaxed">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                    @endforeach
                </div>

            <div class="mt-8 text-center">
                <a href="#plans">
                <button class="cta-btn">Ver Opções e Começar</button>
                </a>
                <div class="mt-3 flex items-center justify-center text-xs" style="color:var(--muted);font-weight:600">
                    <i class="fa-regular fa-circle-check mr-1"></i>
                    Proteja seu negócio - 100% seguro e legal
                </div>
            </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12" style="border-top:1px solid rgba(255,255,255,0.03);">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--muted)">
                    &copy; {{ date('Y') }} CLIVUS. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script>
        tsParticles.load("hero-particles", {
            fpsLimit: 60,
            particles: {
                number: { value: 40 },
                color: { value: ["#8b5cf6","#00b4cc","#ffffff"] },
                shape: { type: "circle" },
                opacity: { value: 0.08 },
                size: { value: { min: 2, max: 8 } },
                move: { enable: true, speed: 0.6, direction: "none", outModes: { default: "out" } }
            },
            interactivity: {
                events: {
                    onHover: { enable: true, mode: "grab" },
                    onClick: { enable: false }
                },
                modes: { grab: { distance: 140, links: { opacity: 0.1 } } }
            },
            detectRetina: true
        });

        document.addEventListener('DOMContentLoaded', function(){
            const cards = document.querySelectorAll('.plan-card');
            cards.forEach((c,i)=> {
                c.style.opacity = 0;
                c.style.transform = 'translateY(18px)';
                setTimeout(()=> {
                    c.style.transition = 'opacity .6s ease-out, transform .6s cubic-bezier(.2,.9,.35,1)';
                    c.style.opacity = 1;
                    c.style.transform = 'translateY(0)';
                }, 120 * i);
            });
        });
    </script>
</body>
</html>