<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'PrismaHUB') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <script src="//unpkg.com/alpinejs" defer></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: { extend: { colors: { dark: '#0F172A', card: '#1E293B', primary: '#3483FA' } } }
            }
        </script>
        <style>[x-cloak] { display: none !important; }</style>
    </head>
    <body class="font-sans antialiased bg-dark text-slate-300">
        <div class="flex h-screen overflow-hidden">
            @include('components.sidebar')

            <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>