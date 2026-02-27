<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900">Mes colocations</h2>
            <p class="text-sm text-slate-500">Retrouvez votre colocation active et votre historique.</p>
        </div>
        <a href="{{ route('colocations.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
            + Nouvelle colocation
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h3 class="text-lg font-semibold text-slate-900">Colocation active</h3>

            @if($activeColocation)
                <div class="mt-4 rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-base font-semibold text-slate-900">{{ $activeColocation->name }}</h4>
                        <span class="rounded-md bg-emerald-100 px-2 py-1 text-xs font-semibold uppercase text-emerald-700">Active</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-600">Rôle: {{ $activeMembership?->role }}</p>
                    <a href="{{ route('colocations.show', $activeColocation) }}" class="mt-4 inline-flex rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Ouvrir la colocation
                    </a>
                </div>
            @else
                <p class="mt-4 text-sm text-slate-500">Aucune colocation active.</p>
            @endif
        </section>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h3 class="text-lg font-semibold text-slate-900">Historique</h3>

            <div class="mt-4 space-y-3">
                @forelse($historyMemberships as $membership)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-medium text-slate-900">{{ $membership->colocation?->name ?? 'Colocation supprimée' }}</p>
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold uppercase text-slate-600">
                                {{ $membership->colocation?->status ?? 'inactive' }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            Rejoint: {{ $membership->joined_at ?? '-' }}
                            | Sorti: {{ $membership->left_at ?? '-' }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aucun historique pour le moment.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
