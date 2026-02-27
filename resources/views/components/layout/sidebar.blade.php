@php
    $user = auth()->user();
    $linkBase = 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition';
    $activeBase = 'bg-indigo-50 text-indigo-600 font-medium';
    $inactiveBase = 'text-gray-600 hover:bg-gray-50';
@endphp

<div class="flex h-full flex-col bg-white">
    <div class="flex h-16 items-center border-b border-gray-200 px-4">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <svg class="h-5 w-5 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 10.5 12 3l9 7.5" />
                <path d="M5.5 9.5V20h13V9.5" />
            </svg>
            <span class="text-xl font-bold text-indigo-500">EasyColoc</span>
        </a>
    </div>

    <nav class="space-y-1 p-3">
        <a href="{{ route('dashboard') }}" class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? $activeBase : $inactiveBase }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 11.5 12 4l9 7.5" />
                <path d="M6 10v10h12V10" />
            </svg>
            Dashboard
        </a>

        <a href="{{ route('colocations.index') }}" class="{{ $linkBase }} {{ request()->routeIs('colocations.*') ? $activeBase : $inactiveBase }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="8" r="2" />
                <circle cx="15" cy="8" r="2" />
                <path d="M3.5 18c.5-2.5 2.3-4 5.5-4" />
                <path d="M20.5 18c-.5-2.5-2.3-4-5.5-4" />
                <path d="M8 18h8" />
            </svg>
            Colocations
        </a>

        @if($user?->is_admin)
            <a href="{{ route('admin.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.*') ? $activeBase : $inactiveBase }}">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 3 4 7v6c0 5 3.5 8 8 8s8-3 8-8V7l-8-4Z" />
                    <path d="M9 12h6" />
                </svg>
                Admin
            </a>
        @endif

        <a href="{{ route('profile.edit') }}" class="{{ $linkBase }} {{ request()->routeIs('profile.*') ? $activeBase : $inactiveBase }}">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="7" r="4" />
                <path d="M4 21c0-3.5 3.5-6 8-6s8 2.5 8 6" />
            </svg>
            Profile
        </a>
    </nav>

    <div class="mt-auto border-t border-gray-200 p-3">
        <div class="mb-3 rounded-xl bg-gray-900 p-3 text-white">
            <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">VOTRE RÉPUTATION</p>
            <p class="mt-1 text-xl font-bold">+{{ $user?->reputation ?? 0 }} points</p>
            <div class="mt-2 h-1.5 rounded-full bg-gray-700">
                <div class="h-full w-1/2 rounded-full bg-emerald-500"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Logout
            </button>
        </form>
    </div>
</div>
