<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="scroll-smooth">
    <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes" />

    <title>{{ $title ?? 'Messanger' }}</title>

    @vite(['resources/css/app.css'])

    @livewireStyles()

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <style>
        body {
            font-feature-settings: "liga", "dlig";
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    </head>
    <body class="font-vasir min-h-screen  items-center gap-1 bg-gray-50 text-gray-900">

    {{ $slot }}

    @livewireScripts()

    @vite(['resources/js/app.js'])

    @stack('scripts')

    <x-toaster-hub />

    </body>
</html>
