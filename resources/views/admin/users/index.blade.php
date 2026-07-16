@extends('layouts.admin')

@section('title', 'Users · Third Down Sports')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold text-slate-500">People</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Users</h1>
        <p class="mt-1 text-sm text-neutral-500">Coaches, parents and administrators.</p>
    </div>
    <a href="{{ route('admin.users.create', ['role' => $role ?: 'coach']) }}" class="inline-flex min-h-11 shrink-0 cursor-pointer items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">+ New user</a>
</div>

<div class="mt-6 flex flex-wrap gap-2">
    @foreach ([
        ['key' => null, 'label' => 'All', 'count' => $counts['all']],
        ['key' => 'coach', 'label' => 'Coaches', 'count' => $counts['coach']],
        ['key' => 'parent', 'label' => 'Parents', 'count' => $counts['parent']],
        ['key' => 'admin', 'label' => 'Admins', 'count' => $counts['admin']],
    ] as $tab)
        @php $active = $role === $tab['key']; @endphp
        <a href="{{ route('admin.users.index', $tab['key'] ? ['role' => $tab['key']] : []) }}"
            class="cursor-pointer rounded-full px-3.5 py-1.5 text-sm font-semibold transition {{ $active ? 'bg-slate-900 text-white' : 'bg-white text-neutral-600 ring-1 ring-neutral-200 hover:bg-neutral-50' }}">
            {{ $tab['label'] }} <span class="{{ $active ? 'text-white/60' : 'text-neutral-400' }}">{{ $tab['count'] }}</span>
        </a>
    @endforeach
</div>

@if ($users->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
        <p class="text-sm text-neutral-500">No users in this view.</p>
    </div>
@else
    <div class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50/80 text-left text-xs uppercase tracking-wider text-neutral-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="hidden px-5 py-4 font-semibold sm:table-cell">Role</th>
                    <th class="hidden px-5 py-4 font-semibold md:table-cell">Details</th>
                    <th class="hidden px-5 py-4 font-semibold sm:table-cell">Status</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($users as $user)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="cursor-pointer font-semibold text-neutral-900 hover:text-slate-700">{{ $user->name }}</a>
                            <div class="text-xs text-neutral-500">{{ $user->email }}</div>
                            <div class="mt-1 sm:hidden"><x-role-badge :role="$user->role" /></div>
                        </td>
                        <td class="hidden px-5 py-4 sm:table-cell"><x-role-badge :role="$user->role" /></td>
                        <td class="hidden px-5 py-4 text-neutral-600 md:table-cell">
                            @if ($user->role === 'coach')
                                {{ $user->classes_count }} {{ Str::plural('class', $user->classes_count) }}
                            @elseif ($user->role === 'parent')
                                {{ $user->students_count }} {{ Str::plural('child', $user->students_count) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="hidden px-5 py-4 sm:table-cell">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $user->is_active ? 'text-emerald-700' : 'text-neutral-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-neutral-300' }}"></span>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}" class="cursor-pointer font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cursor-pointer font-semibold text-red-600 hover:text-red-700">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
