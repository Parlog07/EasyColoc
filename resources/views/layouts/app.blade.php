<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyColoc') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
            <aside class="hidden lg:flex w-[240px] h-screen shrink-0 bg-white border-r border-gray-200 sticky top-0">
                <x-layout.sidebar />
            </aside>

            <div
                class="fixed inset-0 z-40 bg-black/40 lg:hidden"
                x-show="sidebarOpen"
                x-transition.opacity
                @click="sidebarOpen = false"
            ></div>

            <aside
                class="fixed left-0 top-0 z-50 h-full w-[240px] bg-white border-r border-gray-200 transition-transform lg:hidden"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <x-layout.sidebar />
            </aside>

            <main class="flex-1 min-w-0 bg-gray-50 overflow-auto h-screen">
                <x-layout.header :title="isset($header) ? trim(strip_tags($header)) : null" />

                <div class="p-4 md:p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
