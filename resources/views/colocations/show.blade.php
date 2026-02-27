<x-app-layout>
    <div class="max-w-4xl mx-auto p-6">
        @if(auth()->id() === $colocation->owner_id && $colocation->status === 'active')
        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}" class="mb-6">
            @csrf
            <button type="submit"
                    class="px-4 py-2 bg-red-600 text-white rounded"
                    onclick="return confirm('Are you sure you want to cancel this colocation?')">
                Cancel Colocation
            </button>
        </form>
        @endif 


        @if(auth()->id() === $colocation->owner_id && $colocation->status === 'active')
        <div class="mb-6 p-4 border rounded">
            <h2 class="font-semibold mb-2">Invite a member</h2>

            <form method="POST" action="{{ route('invitations.store', $colocation) }}">
                @csrf

                <div class="mb-3">
                    <label class="block mb-1">Email</label>
                    <input name="email" type="email" class="w-full border rounded p-2" required />
                    @error('email')
                        <p class="text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Send Invitation
                </button>
            </form>

            @if(session('success'))
                <p class="text-green-600 mt-3">{{ session('success') }}</p>
            @endif
        </div>
    @endif

    @if($pendingInvites->count())
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Pending invitations</h2>

        <ul class="space-y-2">
            @foreach($pendingInvites as $invite)
                <li class="border rounded p-3 flex justify-between">
                    <span>{{ $invite->email }}</span>
                    <span class="text-gray-600">expires: {{ $invite->expires_at }}</span>
                </li>
            @endforeach
        </ul>
    </div>
    @endif
    
@if($colocation->status === 'active')
    <div class="mt-8 p-4 border rounded">
        <h2 class="text-xl font-semibold mb-4">Add Expense</h2>

        <form method="POST" action="{{ route('expenses.store', $colocation) }}">
            @csrf

            <div class="mb-3">
                <label class="block">Title</label>
                <input name="title" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block">Amount</label>
                <input type="number" step="0.01" name="amount" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block">Date</label>
                <input type="date" name="expense_date" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-3">
                <label class="block">Category</label>
                <select name="category_id" class="w-full border rounded p-2" required>
                    @foreach($colocation->categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="px-4 py-2 bg-green-600 text-white rounded">
                Add Expense
            </button>
        </form>
    </div>
@endif

@if(isset($balances))
    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-3">Balances</h2>

        <p>Total expenses: <b>{{ number_format($totalExpenses, 2) }}</b></p>
        <p>Share per member: <b>{{ number_format($share, 2) }}</b></p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2 text-left">Member</th>
                        <th class="border p-2 text-right">Paid</th>
                        <th class="border p-2 text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balances as $b)
                        <tr>
                            <td class="border p-2">{{ $b['user']->name }}</td>
                            <td class="border p-2 text-right">{{ number_format($b['paid'], 2) }}</td>
                            <td class="border p-2 text-right 
                                @if($b['balance'] < 0) text-red-600 @else text-green-600 @endif">
                                {{ number_format($b['balance'], 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif


@if(isset($expenses))
    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-3">Expenses</h2>

        @if($expenses->count() === 0)
            <p class="text-gray-600">No expenses yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2 text-left">Date</th>
                            <th class="border p-2 text-left">Title</th>
                            <th class="border p-2 text-left">Category</th>
                            <th class="border p-2 text-left">Payer</th>
                            <th class="border p-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $e)
                            <tr>
                                <td class="border p-2">{{ $e->expense_date }}</td>
                                <td class="border p-2">{{ $e->title }}</td>
                                <td class="border p-2">{{ $e->category->name ?? '-' }}</td>
                                <td class="border p-2">{{ $e->payer->name ?? $e->payer->email }}</td>
                                <td class="border p-2 text-right">{{ number_format($e->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif


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
        @if(auth()->id() !== $colocation->owner_id && $colocation->status === 'active')
        <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
            @csrf
            <button class="px-4 py-2 bg-yellow-500 text-white rounded"
                    onclick="return confirm('Leave this colocation?')">
                Leave Colocation
            </button>
        </form>
        @endif
    </div>
</x-app-layout>