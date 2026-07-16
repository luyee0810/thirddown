@extends('layouts.admin')

@section('title', 'Admin · Third Down Sports')

@section('content')
<div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-sm font-semibold text-slate-500">Site administration</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Welcome, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-neutral-500">Manage users, students and classes across the whole site.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex min-h-11 shrink-0 cursor-pointer items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">+ New user</a>
</div>

<div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-3">
    @foreach ([
        ['value' => $stats['coaches'], 'label' => 'Coaches', 'url' => route('admin.users.index', ['role' => 'coach'])],
        ['value' => $stats['parents'], 'label' => 'Parents', 'url' => route('admin.users.index', ['role' => 'parent'])],
        ['value' => $stats['admins'], 'label' => 'Admins', 'url' => route('admin.users.index', ['role' => 'admin'])],
        ['value' => $stats['students'], 'label' => 'Students', 'url' => route('admin.students.index')],
        ['value' => $stats['classes'], 'label' => 'Classes', 'url' => route('admin.classes.index')],
        ['value' => $stats['sessions'], 'label' => 'Sessions', 'url' => route('admin.classes.index')],
    ] as $stat)
        <a href="{{ $stat['url'] }}" class="group cursor-pointer rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-md sm:p-5">
            <div class="text-2xl font-bold sm:text-3xl">{{ $stat['value'] }}</div>
            <div class="mt-1 text-xs font-medium text-neutral-500 sm:text-sm">{{ $stat['label'] }}</div>
        </a>
    @endforeach
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
            <h2 class="text-lg font-bold tracking-tight">Recent users</h2>
            <a href="{{ route('admin.users.index') }}" class="cursor-pointer text-sm font-semibold text-slate-600 hover:text-slate-900">View all →</a>
        </div>
        @if ($recentUsers->isEmpty())
            <div class="p-8 text-center text-sm text-neutral-500">No users yet.</div>
        @else
            <ul class="divide-y divide-neutral-100">
                @foreach ($recentUsers as $user)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <a href="{{ route('admin.users.edit', $user) }}" class="cursor-pointer truncate font-semibold text-neutral-900 hover:text-slate-700">{{ $user->name }}</a>
                            <div class="truncate text-xs text-neutral-500">{{ $user->email }}</div>
                        </div>
                        <x-role-badge :role="$user->role" />
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <div class="border-b border-neutral-100 px-5 py-4">
            <h2 class="text-lg font-bold tracking-tight">Upcoming sessions</h2>
        </div>
        @if ($upcoming->isEmpty())
            <div class="p-8 text-center text-sm text-neutral-500">No upcoming sessions.</div>
        @else
            <ul class="divide-y divide-neutral-100">
                @foreach ($upcoming as $session)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <div class="truncate font-semibold text-neutral-900">{{ $session->trainingClass->name }}</div>
                            <div class="truncate text-xs text-neutral-500">Coach {{ $session->trainingClass->coach?->name ?? '—' }}</div>
                        </div>
                        <span class="shrink-0 text-sm text-neutral-500">{{ $session->session_date->format('D, M j') }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
@endsection
