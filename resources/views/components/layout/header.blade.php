@php
    $user = auth()->user();
    $titleValue = strtoupper($title ?: 'TABLEAU DE BORD');
    $avatar = strtoupper(substr($user?->name ?? 'A', 0, 1));
@endphp

<header class="sticky top-0 z-10 border-b border-gray-200 bg-white">
    <div class="relative flex h-16 items-center justify-between px-4 md:px-6">
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="rounded-md p-2 text-gray-600 hover:bg-gray-50 lg:hidden"
                @click="sidebarOpen = true"
                aria-label="Ouvrir le menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <h1 class="text-lg font-bold text-gray-900">{{ $titleValue }}</h1>
        </div>

        <a
            href="{{ route('colocations.create') }}"
            class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 sm:inline-flex"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Nouvelle colocation
        </a>

        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold uppercase leading-tight text-emerald-500">
                {{ $user?->is_admin ? 'ADMIN' : 'MEMBRE' }}
                <br>
                EN LIGNE
            </span>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-900 text-sm font-bold text-white">{{ $avatar }}</div>
        </div>
    </div>

    <div class="border-t border-gray-200 px-4 py-2 sm:hidden">
        <a
            href="{{ route('colocations.create') }}"
            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14" />
            </svg>
            Nouvelle colocation
        </a>
    </div>
</header>
