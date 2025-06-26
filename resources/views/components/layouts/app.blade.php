<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite([
            'resources/css/app.css',
        ])
        <title>{{ $title ?? 'Messanger' }}</title>
        @livewireStyles()
    </head>
    <body class=" font-vasir items-center flex flex-col gap-1 ">
        {{ $slot }}
        @livewireScripts()
        @vite([
            'resources/js/app.js'
        ])
        @stack('scripts')
    </body>
</html>
