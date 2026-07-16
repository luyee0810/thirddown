@extends('layouts.app')

@section('body')
@php
    $nav = [
        'admin.dashboard' => ['Dashboard', route('admin.dashboard')],
        'admin.users' => ['Users', route('admin.users.index')],
        'admin.students' => ['Students', route('admin.students.index')],
        'admin.classes' => ['Classes', route('admin.classes.index')],
    ];
@endphp

<div class="min-h-full bg-[#f6f7f9]">
    <header class="sticky top-0 z-30 border-b border-slate-800 bg-slate-900 text-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-8">
                <a href="{{ route('admin.dashboard') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Third Down Sports admin">
                    <x-app-logo class="h-9 w-auto brightness-0 invert" />
                    <span class="rounded-md bg-white/10 px-2 py-0.5 text-[11px] font-bold uppercase tracking-wider text-white/80">Admin</span>
                </a>
                <nav class="hidden items-center gap-1 md:flex" aria-label="Primary navigation">
                    @foreach ($nav as $key => [$label, $url])
                        @php $active = request()->routeIs($key.'*'); @endphp
                        <a href="{{ $url }}"
                            @if ($active) aria-current="page" @endif
                            class="relative cursor-pointer rounded-lg px-3.5 py-2 text-sm font-semibold transition {{ $active ? 'bg-white/15 text-white' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden text-sm font-medium text-slate-200 sm:inline">{{ auth()->user()->name }}</span>
                <span class="hidden h-8 w-8 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white sm:flex" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="cursor-pointer rounded-lg border border-white/20 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:border-white/40 hover:bg-white/10 hover:text-white">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 pb-28 sm:px-6 sm:py-8 md:pb-10 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-800 bg-slate-900 px-2 pb-[max(.5rem,env(safe-area-inset-bottom))] pt-2 text-white shadow-[0_-8px_30px_rgba(0,0,0,.15)] md:hidden" aria-label="Mobile navigation">
        <div class="mx-auto grid max-w-md grid-cols-4">
            @foreach ($nav as $key => [$label, $url])
                @php $active = request()->routeIs($key.'*'); @endphp
                <a href="{{ $url }}" @if ($active) aria-current="page" @endif
                    class="flex min-h-14 cursor-pointer flex-col items-center justify-center gap-1 rounded-xl text-[11px] font-semibold transition {{ $active ? 'bg-white/15 text-white' : 'text-slate-400' }}">
                    @if ($key === 'admin.dashboard')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10M9 20v-6h6v6"/></svg>
                    @elseif ($key === 'admin.users')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    @elseif ($key === 'admin.students')
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/></svg>
                    @endif
                    <span>{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </nav>
</div>
@endsection
