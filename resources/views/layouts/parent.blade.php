@extends('layouts.app')

@section('body')
<div class="min-h-full bg-[#f8f8f7]">
    <header class="sticky top-0 z-30 border-b border-neutral-200/90 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('parent.dashboard') }}" class="flex shrink-0 items-center" aria-label="Third Down Sports home">
                <x-app-logo class="h-9 w-auto" />
            </a>
            <div class="flex items-center gap-3">
                <span class="hidden text-sm font-medium text-neutral-700 sm:inline">{{ auth()->user()->name }}</span>
                <span class="hidden h-8 w-8 items-center justify-center rounded-full bg-neutral-100 text-sm font-bold text-neutral-600 sm:flex" aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="rounded-lg border border-neutral-200 px-3 py-2 text-sm font-semibold text-neutral-600 transition hover:border-neutral-300 hover:bg-neutral-50 hover:text-neutral-900">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
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
</div>
@endsection
