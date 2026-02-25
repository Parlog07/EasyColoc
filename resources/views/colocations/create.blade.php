<x-app-layout>
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Create a Colocation</h1>

        <form method="POST" action="{{ route('colocations.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Colocation name</label>
                <input
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full border rounded p-2"
                    required
                />
                @error('name')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button class="px-4 py-2 bg-black text-white rounded">
                Create
            </button>
        </form>
    </div>
</x-app-layout>