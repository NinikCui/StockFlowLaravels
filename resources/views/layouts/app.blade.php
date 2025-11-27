<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js" defer></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                AOS.init({
                    duration: 700,
                    once: true,
                    easing: 'ease-out-quart'
                });
            });
        </script>
    </head>
    <body class="bg-gray-50">
    
        @php
            $scope = session('role.scope'); 
        @endphp
        <p>{{ $scope }}</p>
        {{-- SIDEBAR --}}
        @if ($scope === 'COMPANY')
            @include('components.sidebarCompany')

        @elseif($scope === 'BRANCH')
            @include('components.sidebarBranch')

        @endif
    <main class="md:ml-64 min-h-screen p-6">
            <div class="mx-auto max-w-6xl p-6 min-h-screen">
{{ $slot }}
            </div>
        
    </main>

</body>
</html>
