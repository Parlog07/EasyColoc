<x-app-layout>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900">Admin Dashboard</h1>
      <p class="text-sm text-slate-500">Gestion globale de la plateforme.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Users</p>
        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['users'] }}</p>
      </div>
      <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Colocations</p>
        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['colocations'] }}</p>
      </div>
      <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Expenses</p>
        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['expenses'] }}</p>
      </div>
      <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
        <p class="text-sm text-slate-500">Banned</p>
        <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['banned'] }}</p>
      </div>
    </div>

    @if(session('success'))
      <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
    @endif

    <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
      <h2 class="mb-4 text-lg font-semibold text-slate-900">Users</h2>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="border-b border-slate-200 text-left uppercase tracking-wide text-slate-400">
              <th class="pb-3 font-semibold">Name</th>
              <th class="pb-3 font-semibold">Email</th>
              <th class="pb-3 font-semibold">Admin</th>
              <th class="pb-3 font-semibold">Banned</th>
              <th class="pb-3 font-semibold">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $u)
              <tr class="border-b border-slate-100 last:border-none">
                <td class="py-3 font-medium text-slate-900">{{ $u->name }}</td>
                <td class="py-3 text-slate-700">{{ $u->email }}</td>
                <td class="py-3">
                  <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $u->is_admin ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                    {{ $u->is_admin ? 'yes' : 'no' }}
                  </span>
                </td>
                <td class="py-3">
                  <span class="rounded-md px-2 py-1 text-xs font-semibold {{ $u->is_banned ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                    {{ $u->is_banned ? 'yes' : 'no' }}
                  </span>
                </td>
                <td class="py-3">
                  @if(!$u->is_banned)
                    <form method="POST" action="{{ route('admin.ban', $u) }}">
                      @csrf
                      <button class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-500">Ban</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('admin.unban', $u) }}">
                      @csrf
                      <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">Unban</button>
                    </form>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
  </div>
</x-app-layout>
