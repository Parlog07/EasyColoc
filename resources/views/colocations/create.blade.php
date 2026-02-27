<x-app-layout>
    <div class="mx-auto max-w-2xl">
        <div class="mb-4">
            <a href="{{ route('colocations.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">← Retour aux colocations</a>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 sm:p-8">
            <h1 class="text-2xl font-semibold text-slate-900">Créer une nouvelle colocation</h1>
            <p class="mt-1 text-sm text-slate-500">Démarrez votre espace de partage des dépenses.</p>

            <form method="POST" action="{{ route('colocations.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nom de la colocation</label>
                    <input
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required
                    />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Créer la colocation
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
