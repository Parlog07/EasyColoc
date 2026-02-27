<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <a href="{{ route('colocations.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">← Retour aux colocations</a>
                <h1 class="mt-1 text-2xl font-semibold text-slate-900">{{ $colocation->name }}</h1>
            </div>
            <span class="rounded-md px-3 py-1 text-xs font-semibold uppercase {{ $colocation->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                {{ $colocation->status }}
            </span>
        </div>

        @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100 lg:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Membres</h2>
                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">{{ $members->count() }} membres</span>
                </div>

                <ul class="space-y-2">
                    @foreach($members as $member)
                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 px-4 py-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $member->name }}</p>
                                <p class="text-sm text-slate-500">{{ $member->email }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="rounded-md bg-indigo-50 px-2 py-1 text-xs font-semibold uppercase text-indigo-700">{{ $member->pivot->role }}</span>

                                @if(auth()->id() === $colocation->owner_id && $member->id !== $colocation->owner_id)
                                    <form method="POST" action="{{ route('colocations.transfer-ownership', [$colocation, $member]) }}">
                                        @csrf
                                        <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500"
                                                onclick="return confirm('Transfer ownership to this member?')">
                                            Transfer Ownership
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('colocations.members.remove', [$colocation, $member]) }}">
                                        @csrf
                                        <button class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500"
                                                onclick="return confirm('Remove this member?')">
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 flex flex-wrap gap-2">
                    @if(auth()->id() === $colocation->owner_id && $colocation->status === 'active')
                        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                            @csrf
                            <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500" onclick="return confirm('Are you sure you want to cancel this colocation?')">
                                Cancel Colocation
                            </button>
                        </form>
                    @endif

                    @if(auth()->id() !== $colocation->owner_id && $colocation->status === 'active')
                        <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                            @csrf
                            <button class="rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-400" onclick="return confirm('Leave this colocation?')">
                                Leave Colocation
                            </button>
                        </form>
                    @endif
                </div>
            </section>

            <aside class="space-y-6">
                @if(auth()->id() === $colocation->owner_id && $colocation->status === 'active')
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <h2 class="mb-3 text-lg font-semibold text-slate-900">Invite a member</h2>
                        <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="space-y-3">
                            @csrf
                            <input name="email" type="email" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="email@example.com" required />
                            <button class="w-full rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Send Invitation</button>
                        </form>
                    </section>
                @endif

                @if($pendingInvites->count())
                    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <h2 class="mb-3 text-lg font-semibold text-slate-900">Pending invitations</h2>
                        <ul class="space-y-2 text-sm">
                            @foreach($pendingInvites as $invite)
                                <li class="rounded-lg border border-slate-200 px-3 py-2">
                                    <p class="font-medium text-slate-800">{{ $invite->email }}</p>
                                    <p class="text-xs text-slate-500">Expires: {{ $invite->expires_at }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>

        @if($colocation->status === 'active')
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Add Expense</h2>

                <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                    @csrf
                    <input name="title" value="{{ old('title') }}" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Title" required>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Amount" required>
                    <input type="date" name="expense_date" value="{{ old('expense_date') }}" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>

                    <select name="category_id" id="category_id" class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select category</option>
                        @foreach($colocation->categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                        <option value="__new__" @selected(old('category_id') == '__new__')>Autre (nouvelle catégorie)</option>
                    </select>

                    <input
                        type="text"
                        name="category_name"
                        id="category_name"
                        value="{{ old('category_name') }}"
                        class="rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 md:col-span-2 lg:col-span-2"
                        placeholder="Nouvelle catégorie (ex: Courses, Internet, Eau...)"
                    >

                    <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500 md:col-span-2 lg:col-span-1">
                        Add Expense
                    </button>
                </form>

                <script>
                    (function () {
                        const select = document.getElementById('category_id');
                        const input = document.getElementById('category_name');
                        if (!select || !input) return;

                        const syncState = () => {
                            const isNew = select.value === '__new__';
                            input.disabled = !isNew;
                            input.required = isNew;
                            if (!isNew) {
                                input.value = '';
                            }
                        };

                        syncState();
                        select.addEventListener('change', syncState);
                    })();
                </script>
            </section>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">Balances</h2>
            <p class="mt-1 text-sm text-slate-500">Total expenses: <span class="font-semibold text-slate-800">{{ number_format($totalExpenses, 2) }}</span> | Share per member: <span class="font-semibold text-slate-800">{{ number_format($share, 2) }}</span></p>

            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left uppercase tracking-wide text-slate-400">
                            <th class="pb-3 font-semibold">Member</th>
                            <th class="pb-3 text-right font-semibold">Paid</th>
                            <th class="pb-3 text-right font-semibold">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($balances as $b)
                            <tr class="border-b border-slate-100 last:border-none">
                                <td class="py-3 font-medium text-slate-900">{{ $b['user']->name }}</td>
                                <td class="py-3 text-right text-slate-700">{{ number_format($b['paid'], 2) }}</td>
                                <td class="py-3 text-right font-semibold {{ $b['balance'] < 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format($b['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        @if(isset($settlements) && count($settlements) > 0)
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Who Owes Who</h2>

                <ul class="space-y-2">
                    @foreach($settlements as $s)
                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 p-3">
                            <div>
                                <b>{{ $s['from']->name }}</b> owes <b>{{ $s['to']->name }}</b>
                                <span class="font-semibold text-emerald-600">{{ number_format($s['amount'], 2) }}</span>
                            </div>

                            @if(auth()->id() === $s['from']->id)
                                <form method="POST" action="{{ route('payments.store', $colocation) }}">
                                    @csrf
                                    <input type="hidden" name="from_user_id" value="{{ $s['from']->id }}">
                                    <input type="hidden" name="to_user_id" value="{{ $s['to']->id }}">
                                    <input type="hidden" name="amount" value="{{ round($s['amount'], 2) }}">
                                    <button class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">Mark Paid</button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Expenses</h2>
                <form method="GET" class="flex flex-wrap items-end gap-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-500">Month</label>
                        <select name="month" class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" @selected(request('month') == $m)>{{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase text-slate-500">Year</label>
                        <input name="year" type="number" class="w-24 rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ request('year') }}">
                    </div>
                    <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Filter</button>
                </form>
            </div>

            @if($expenses->count() === 0)
                <p class="text-sm text-slate-500">No expenses yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 text-left uppercase tracking-wide text-slate-400">
                                <th class="pb-3 font-semibold">Date</th>
                                <th class="pb-3 font-semibold">Title</th>
                                <th class="pb-3 font-semibold">Category</th>
                                <th class="pb-3 font-semibold">Payer</th>
                                <th class="pb-3 text-right font-semibold">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expenses as $e)
                                <tr class="border-b border-slate-100 last:border-none">
                                    <td class="py-3 text-slate-700">{{ $e->expense_date }}</td>
                                    <td class="py-3 font-medium text-slate-900">{{ $e->title }}</td>
                                    <td class="py-3 text-slate-700">{{ $e->category->name ?? '-' }}</td>
                                    <td class="py-3 text-slate-700">{{ $e->payer->name ?? $e->payer->email }}</td>
                                    <td class="py-3 text-right font-semibold text-slate-900">{{ number_format($e->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
