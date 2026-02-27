<x-app-layout>
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-2">Invitation</h1>

        <p class="mb-2">Colocation ID: {{ $invitation->colocation_id }}</p>
        <p class="mb-2">Status: <b>{{ $invitation->status }}</b></p>
        <p class="mb-6">Expires at: {{ $invitation->expires_at }}</p>

        @if($invitation->status === 'expired')
    <p class="text-red-600 font-semibold">This invitation has expired.</p>
    @endif

        @if($invitation->status === 'pending')
            <div class="flex gap-3">
                <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded">Accept</button>
                </form>

                <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white rounded">Refuse</button>
                </form>
            </div>
        @endif

        @error('msg')
            <p class="text-red-600 mt-4">{{ $message }}</p>
        @enderror
    </div>
</x-app-layout>