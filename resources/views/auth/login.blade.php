<!DOCTYPE html>
<html lang="pt-BR" data-theme="carbon-pro" data-color-mode="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - CLIVUS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --bg-900: #05060a;
            --bg-800: #0b1220;
            --accent: #8b5cf6;
            --accent-2: #00b4cc;
            --glass: rgba(255,255,255,0.04);
            --muted: #9aa8bf;
        }
        body{background:
            radial-gradient(600px 300px at 10% 10%, rgba(139,92,246,0.06), transparent),
            linear-gradient(180deg,var(--bg-900),var(--bg-800));
            color:#e6eef8;
            -webkit-font-smoothing:antialiased;
        }
        .card-glass{
            background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
            border: 1px solid rgba(255,255,255,0.04);
            box-shadow: 0 12px 40px rgba(2,6,23,0.7);
            border-radius: 1rem;
            backdrop-filter: blur(6px);
        }
        .accent-gradient{
            background: linear-gradient(90deg,var(--accent),var(--accent-2));
            -webkit-background-clip:text;background-clip:text;color:transparent;font-weight:800;
        }
        .muted{color:var(--muted)}
        #login-particles{position:fixed;inset:0;z-index:0;pointer-events:none}
        @media (prefers-reduced-motion: reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative">
    <div id="login-particles"></div>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="{{ asset('assets/logo.png') }}" alt="CLIVUS" class="h-20 w-auto">
            </div>
            <h1 class="text-4xl font-bold mb-2" style="color: rgb(139, 92, 246);">CLIVUS</h1>
            <p class="text-sm font-medium" style="color: rgb(107, 114, 128);">Sistema Financeiro Completo</p>
        </div>

        <div class="card-glass p-8">
            <h2 class="text-2xl font-bold mb-6 text-center accent-gradient">Entrar</h2>

            @if(session('message'))
            <div class="mb-4 p-4 rounded-lg" style="background-color: rgba(59, 130, 246, 0.1); border: 1px solid rgb(59, 130, 246); color: rgb(37, 99, 235);">
                <p class="text-sm">{{ session('message') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-4 rounded-lg" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgb(239, 68, 68); color: rgb(220, 38, 38);">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                @if(session('invitation_token'))
                <input type="hidden" name="invitation_token" value="{{ session('invitation_token') }}">
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium mb-2" style="color: #e6eef8;">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border transition-colors focus:outline-none focus:ring-2"
                        style="background-color: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); color: #e6eef8; --tw-ring-color: rgba(139,92,246,0.35);"
                        placeholder="seu@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-2" style="color: #e6eef8;">Senha</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border transition-colors focus:outline-none focus:ring-2"
                        style="background-color: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); color: #e6eef8; --tw-ring-color: rgba(139,92,246,0.35);"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300"
                            style="border-color: rgba(255,255,255,0.06);">
                        <span class="ml-2 text-sm" style="color: var(--muted);">Lembrar-me</span>
                    </label>
                </div>

                <button type="submit" class="w-full px-6 py-3 rounded-lg font-medium text-white transition-all hover:scale-105 hover:shadow-lg" style="background: linear-gradient(135deg, #8b5cf6, #00b4cc); box-shadow: 0 8px 30px rgba(139,92,246,0.18);">
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center mt-6 text-sm" style="color: rgb(107, 114, 128);">
            Não tem uma conta? <a href="{{ route('public.plans') }}" class="font-medium hover:underline transition-all" style="color: rgb(139, 92, 246);">Veja nossos planos</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script>
        // Theme Management
        const savedTheme = localStorage.getItem('theme') || 'carbon-pro';
        const savedColorMode = localStorage.getItem('colorMode') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.setAttribute('data-color-mode', savedColorMode);

        // particles background
        tsParticles.load("login-particles", {
            particles: {
                number: { value: 30 },
                color: { value: ["#8b5cf6","#00b4cc"] },
                opacity: { value: 0.06 },
                size: { value: { min: 2, max: 7 } },
                move: { enable: true, speed: 0.4, outModes: { default: "out" } }
            },
            interactivity: { events: { onHover: { enable: true, mode: "repulse" } }, modes: { repulse: { distance: 120 } } },
            detectRetina: true
        });
    </script>
</body>
</html>

