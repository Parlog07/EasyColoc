<x-app-layout>
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 17.75 6.5 21l1.5-6.25L3 10.5l6.5-.5L12 4l2.5 6 6.5.5-5 4.25L17.5 21Z" />
                </svg>
            </div>
            <p class="text-sm text-gray-500">Mon score réputation</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ auth()->user()->reputation }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="20" r="1" />
                    <circle cx="17" cy="20" r="1" />
                    <path d="M3 4h2l2 11h11l2-7H7" />
                </svg>
            </div>
            <p class="text-sm text-gray-500">Dépenses Globales ({{ now()->format('M') }})</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ number_format($monthlyTotal, 2, ',', ' ') }} €</p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="xl:col-span-2">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-900">Dépenses récentes</h2>
                @if($activeColocation)
                    <a href="{{ route('colocations.show', $activeColocation) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Voir tout</a>
                @endif
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left uppercase tracking-wide text-gray-500">
                                <th class="px-6 py-4 font-semibold">TITRE / CATÉGORIE</th>
                                <th class="px-6 py-4 font-semibold">PAYEUR</th>
                                <th class="px-6 py-4 font-semibold">MONTANT</th>
                                <th class="px-6 py-4 font-semibold">COLOC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentExpenses as $expense)
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-gray-900">{{ $expense->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $expense->category->name ?? 'Sans catégorie' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">{{ $expense->payer->name }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ number_format($expense->amount, 2, ',', ' ') }} €</td>
                                    <td class="px-6 py-4 text-gray-900">{{ $expense->colocation->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Aucune dépense récente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <aside>
            <div class="rounded-xl bg-gray-800 p-4 text-white shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold">Membres de la coloc</h3>
                    <span class="rounded bg-gray-600 px-2 py-1 text-xs text-white">VOIR</span>
                </div>

                @if($activeColocation)
                    <ul class="space-y-2">
                        @foreach($activeMembers as $member)
                            <li class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2 text-sm">
                                <span>{{ $member->name }}</span>
                                <span class="text-xs uppercase text-gray-300">{{ $member->pivot->role }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-400">Aucune colocation active.</p>
                @endif
            </div>
        </aside>
    </div>

    <div class="fixed bottom-4 left-4 z-40 w-44 rounded-xl bg-gray-900 p-4 text-white shadow-lg md:hidden">
        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-300">VOTRE RÉPUTATION</p>
        <p class="mt-1 text-2xl font-bold">+{{ auth()->user()->reputation }} points</p>
        <div class="mt-3 h-1.5 rounded-full bg-gray-700">
            <div class="h-full w-1/2 rounded-full bg-emerald-500"></div>
        </div>
    </div>
</x-app-layout>
