<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CheckPrint') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Material Symbols (ícones) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles


    <!-- Theme Persistence Script -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</head>
<body class="h-full overflow-hidden transition-colors duration-300">

    <div class="flex h-screen bg-surface">

        <!-- Sidebar -->
        <aside class="group/sidebar frosted-glass w-[280px] h-full flex flex-col rounded-r-3xl my-2 ml-2 transition-all duration-500 ease-in-out border-r-0">
            <!-- Branding -->
            <div class="h-20 flex items-center px-6 gap-3 shrink-0">
                <div class="size-10 rounded-xl bg-brand-500 flex items-center justify-center shadow-lg shadow-brand-500/20">
                    <span class="material-symbols-outlined text-white">print</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-display font-bold text-lg leading-none tracking-tight">CheckPrint</span>
                    <span class="text-[9px] font-black font-sans uppercase tracking-[0.2em] text-muted">GAP Dashboard</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-muted px-4 mb-3">Main Menu</p>

                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-tight transition-all duration-300 relative
                          {{ request()->routeIs('dashboard')
                             ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400 ring-1 ring-brand-500/30'
                             : 'text-secondary hover:bg-white/40 dark:hover:bg-white/5 hover:text-main' }}">
                    @if(request()->routeIs('dashboard'))
                        <div class="absolute left-0 w-1.5 h-7 bg-brand-500 rounded-r-full shadow-[0_0_20px_rgba(59,130,246,0.8)] z-10"></div>
                    @endif
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('dashboard') ? 'text-brand-500 animate-pulse' : 'text-muted group-hover:text-brand-500' }}">dashboard</span>
                    <span class="group-hover:translate-x-1 transition-transform">Dashboards</span>
                </a>

                <a href="{{ route('import') }}"
                   class="group flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-tight transition-all duration-300 relative
                          {{ request()->routeIs('import')
                             ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400 ring-1 ring-brand-500/30'
                             : 'text-secondary hover:bg-white/40 dark:hover:bg-white/5 hover:text-main' }}">
                    @if(request()->routeIs('import'))
                        <div class="absolute left-0 w-1.5 h-7 bg-brand-500 rounded-r-full shadow-[0_0_20px_rgba(59,130,246,0.8)] z-10"></div>
                    @endif
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('import') ? 'text-brand-500 animate-pulse' : 'text-muted group-hover:text-brand-500' }}">cloud_upload</span>
                    <span class="group-hover:translate-x-1 transition-transform">Importar CSV</span>
                </a>

                <a href="{{ route('graficos') }}"
                   class="group flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold uppercase tracking-tight transition-all duration-300 relative
                          {{ request()->routeIs('graficos')
                             ? 'bg-brand-500/10 text-brand-600 dark:text-brand-400 ring-1 ring-brand-500/30'
                             : 'text-secondary hover:bg-white/40 dark:hover:bg-white/5 hover:text-main' }}">
                    @if(request()->routeIs('graficos'))
                        <div class="absolute left-0 w-1.5 h-7 bg-brand-500 rounded-r-full shadow-[0_0_20px_rgba(59,130,246,0.8)] z-10"></div>
                    @endif
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('graficos') ? 'text-brand-500 animate-pulse' : 'text-muted group-hover:text-brand-500' }}">analytics</span>
                    <span class="group-hover:translate-x-1 transition-transform">Indicadores</span>
                </a>
            </nav>

            <!-- User Footer -->
            <div class="p-4 border-t border-glass">
                <div class="p-3 bg-white/20 dark:bg-white/5 rounded-2xl flex items-center gap-3 border border-glass">
                    <div class="size-8 rounded-full bg-brand-500/20 flex items-center justify-center border border-brand-500/30">
                        <span class="material-symbols-outlined text-brand-500 text-sm">person</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] font-bold text-main truncate">GAP Admin</span>
                        <span class="text-[8px] font-black uppercase tracking-widest text-muted">Acessar Perfil</span>
                    </div>
                    <button class="ml-auto p-1.5 text-muted hover:text-main transition-colors">
                        <span class="material-symbols-outlined text-sm">settings</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Header -->
            <header class="frosted-header h-20 px-8 flex items-center justify-between shadow-sm">
                <div>
                    <h2 class="font-display font-bold text-xl tracking-tight">@yield('title', 'Overview')</h2>
                    @hasSection('subtitle')
                        <p class="text-[10px] font-bold text-muted uppercase tracking-widest mt-0.5">@yield('subtitle')</p>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <!-- Search Header (Crystalline Style) -->
                    <div class="hidden md:flex items-center relative group w-64 lg:w-96">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-muted group-focus-within:text-brand-500 transition-colors">search</span>
                        <input type="search" placeholder="Buscar por logs ou printers..."
                               class="w-full bg-white/30 dark:bg-white/5 border border-glass backdrop-blur-md rounded-xl text-xs pl-9 pr-4 py-2 text-main placeholder:text-muted focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500/30 focus:bg-white dark:focus:bg-zinc-900/80 transition-all">
                    </div>

                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="p-2.5 rounded-xl text-muted hover:text-main hover:bg-white/40 dark:hover:bg-white/10 transition-all border border-glass">
                        <span class="material-symbols-outlined text-[20px] dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[20px] hidden dark:block">light_mode</span>
                    </button>

                    <!-- Notifications -->
                    <button class="p-2.5 rounded-xl text-muted hover:text-main hover:bg-white/40 dark:hover:bg-white/10 transition-all border border-glass relative">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-slate-900"></span>
                    </button>

                    @yield('header-actions')
                </div>
            </header>

            <!-- Page Container -->
            <main class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                {{ $slot ?? '' }}
                @yield('content')
            </main>

        </div>
    </div>

    @stack('scripts')
    @livewireScripts
</body>
</html>
