<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">{{ $colocation->name }}</h1>
        <p class="mb-6">Status: <span class="font-semibold">{{ $colocation->status }}</span></p>

        <h2 class="text-xl font-semibold mb-2">Members</h2>
        <ul class="space-y-2">
            @foreach($members as $member)
                <li class="border rounded p-3 flex justify-between">
                    <span>{{ $member->name }} ({{ $member->email }})</span>
                    <span class="text-gray-600">Role: {{ $member->pivot->role }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</x-app-layout>