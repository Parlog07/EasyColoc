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
    <body class="font-sans antialiased text-gray-900">
        <div class="min-h-screen bg-gray-50">
            <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-8">
                <div class="grid w-full max-w-5xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm lg:grid-cols-2">
                    <div class="hidden bg-gray-900 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                        <div>
                            <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 10.5 12 3l9 7.5" />
                                        <path d="M5.5 9.5V20h13V9.5" />
                                    </svg>
                                </span>
                                <span class="text-2xl font-bold">EasyColoc</span>
                            </a>

                            <h1 class="mt-8 text-3xl font-bold leading-tight">Gérez votre colocation simplement</h1>
                            <p class="mt-3 text-sm text-gray-300">
                                Dépenses, invitations, remboursements et réputation dans une seule application.
                            </p>
                        </div>

                        <div class="rounded-xl bg-white/10 p-4 text-sm text-gray-200">
                            <p class="font-semibold text-white">Astuce</p>
                            <p class="mt-1">Invitez vos colocataires par email et suivez automatiquement qui doit quoi.</p>
                        </div>
                    </div>

                    <div class="p-6 sm:p-10">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
