<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gestion RH - ' . config('app.name', 'Laravel'))</title>
    
    <!-- Breeze CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Bootstrap pour tes vues RH -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Alpine.js pour Breeze -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Navigation Breeze -->
    @include('layouts.navigation')

    <!-- Contenu principal - SUPPORT BI-FORMAT -->
    <div class="py-12">
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(isset($slot))
                    {{-- Breeze format --}}
                    {{ $slot }}
                @else
                    {{-- Ton ancien format RH --}}
                    @yield('content')
                @endif
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>