@extends('layouts.coach')

@section('title', 'Dashboard · Third Down Sports')

@section('content')
@php $nextSession = $upcoming->first(); @endphp

<div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <p class="text-sm font-semibold text-brand-600">Coach workspace</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="mt-1 text-sm text-neutral-500">Here’s what’s happening across your classes.</p>
    </div>
</div>

<div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
    <div class="min-w-0">
        <div class="grid grid-cols-3 gap-2 sm:gap-4">
            @foreach ([
                ['value' => $stats['classes'], 'label' => 'Classes', 'url' => route('classes.index'), 'icon' => 'book'],
                ['value' => $stats['students'], 'label' => 'Students', 'url' => route('students.index'), 'icon' => 'people'],
                ['value' => $stats['sessions'], 'label' => 'Sessions', 'url' => route('sessions.index'), 'icon' => 'calendar'],
            ] as $stat)
                <a href="{{ $stat['url'] }}" class="group rounded-2xl border border-neutral-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-200 hover:shadow-md sm:p-5">
                    <div class="flex items-center justify-between">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 sm:h-11 sm:w-11">
                            @if ($stat['icon'] === 'calendar')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                            @elseif ($stat['icon'] === 'people')
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg>
                            @else
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                            @endif
                        </span>
                        <span class="hidden text-neutral-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500 sm:block">→</span>
                    </div>
                    <div class="mt-3 text-2xl font-bold sm:text-3xl">{{ $stat['value'] }}</div>
                    <div class="text-xs font-medium text-neutral-500 sm:text-sm">{{ $stat['label'] }}</div>
                </a>
            @endforeach
        </div>

        <section class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-4 sm:px-6">
                <h2 class="text-lg font-bold tracking-tight">Upcoming sessions</h2>
                <a href="{{ route('sessions.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all →</a>
            </div>
            @if ($upcoming->isEmpty())
                <div class="p-8 text-center text-sm text-neutral-500">No upcoming sessions.</div>
            @else
                <ul class="divide-y divide-neutral-100">
                    @foreach ($upcoming as $session)
                        @include('sessions._row', ['session' => $session])
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <aside class="space-y-5">
        @if ($nextSession)
            <section class="rounded-2xl border border-brand-200 bg-gradient-to-br from-white to-brand-50 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-[.14em] text-brand-600">Next session</p>
                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-neutral-500 shadow-sm">Upcoming</span>
                </div>
                <h2 class="mt-4 text-xl font-bold tracking-tight">{{ $nextSession->trainingClass->name }}</h2>
                <div class="mt-3 space-y-2 text-sm text-neutral-600">
                    <p class="flex items-center gap-2"><span aria-hidden="true">▣</span>{{ $nextSession->session_date->format('D, j M Y') }}</p>
                    @if ($nextSession->start_time)
                        <p class="flex items-center gap-2"><span aria-hidden="true">◷</span>{{ \Carbon\Carbon::parse($nextSession->start_time)->format('g:i A') }}@if ($nextSession->end_time) – {{ \Carbon\Carbon::parse($nextSession->end_time)->format('g:i A') }}@endif</p>
                    @endif
                </div>
                <a href="{{ route('attendance.edit', $nextSession) }}" class="mt-5 flex min-h-11 w-full items-center justify-center rounded-xl bg-brand-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-600">
                    Mark attendance <span class="ml-2">→</span>
                </a>
            </section>
        @endif

        <section class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm">
            <h2 class="px-1 text-sm font-bold text-neutral-900">Quick actions</h2>
            <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
                <a href="{{ route('classes.create') }}" class="flex min-h-16 items-center gap-3 rounded-xl border border-neutral-200 p-3 transition hover:border-brand-200 hover:bg-brand-50/50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-2xl text-brand-600">+</span>
                    <span><strong class="block text-sm">Create class</strong><span class="text-xs text-neutral-500">Single or recurring</span></span>
                    <span class="ml-auto text-neutral-300">→</span>
                </a>
                <a href="{{ route('students.create') }}" class="flex min-h-16 items-center gap-3 rounded-xl border border-neutral-200 p-3 transition hover:border-brand-200 hover:bg-brand-50/50">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-2xl text-brand-600">+</span>
                    <span><strong class="block text-sm">Add student</strong><span class="text-xs text-neutral-500">Build your roster</span></span>
                    <span class="ml-auto text-neutral-300">→</span>
                </a>
            </div>
        </section>
    </aside>
</div>
@endsection
