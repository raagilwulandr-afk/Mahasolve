<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Mahasolve') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Alpine JS & Vite App Assets -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }
            .font-display { font-family: 'Montserrat', sans-serif; }
        </style>
    </head>
    <body class="bg-[#F7F8FC] text-[#16182B] antialiased min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- HEADER LOGO MAHASOLVE -->
        <div class="mb-8 text-center">
            <a href="/" class="inline-flex items-center gap-3 group">
                <span class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition transform"
                      style="background: linear-gradient(135deg, #4F46E5 0%, #14B8A6 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M13 3L4 14H11L11 21L20 10H13L13 3Z" fill="white" stroke="white" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                </span>
                <div class="text-left">
                    <span class="font-display font-extrabold text-2xl tracking-tight text-slate-900 block">Mahasolve</span>
                    <span class="text-[11px] font-semibold text-indigo-600 tracking-wider uppercase block">Solusi Mobilitas & Akademik Unikom</span>
                </div>
            </a>
        </div>

        <!-- AUTH CARD CONTAINER -->
        <div class="w-full sm:max-w-md bg-white border border-[#14162B14] shadow-xl shadow-slate-200/50 rounded-3xl p-8 space-y-6">
            {{ $slot }}
        </div>

        <!-- FOOTER COPYRIGHT -->
        <div class="mt-8 text-center text-xs text-slate-400">
            © 2026 Mahasolve. Dibuat khusus untuk mahasiswa Unikom.
        </div>
    </body>
</html>
