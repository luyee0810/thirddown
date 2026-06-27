@extends('layouts.coach')

@section('title', 'Classes · Third Down Sports')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold text-brand-600">Programme</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Classes</h1>
        <p class="mt-1 text-sm text-neutral-500">Manage rosters and session schedules.</p>
    </div>
    <a href="{{ route('classes.create') }}" class="inline-flex min-h-11 shrink-0 items-center rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-600">+ New class</a>
</div>

@if ($classes->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
        <p class="text-sm text-neutral-500">No classes yet.</p>
        <a href="{{ route('classes.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-600">Create your first class →</a>
    </div>
@else
    <div class="mt-6 hidden overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm md:block">
        <table class="w-full text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50/80 text-left text-xs uppercase tracking-wider text-neutral-500">
                <tr><th class="px-6 py-4 font-semibold">Name</th><th class="px-5 py-4 font-semibold">Type</th><th class="px-5 py-4 font-semibold">Sessions</th><th class="px-5 py-4 font-semibold">Students</th><th class="px-6 py-4"></th></tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($classes as $class)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-6 py-4"><div class="font-semibold text-neutral-900">{{ $class->name }}</div>@if ($class->location)<div class="mt-0.5 text-xs text-neutral-500">{{ $class->location }}</div>@endif</td>
                        <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $class->type === 'regular' ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-700' }}">{{ ucfirst($class->type) }}</span></td>
                        <td class="px-5 py-4 font-medium text-neutral-600">{{ $class->sessions_count }}</td>
                        <td class="px-5 py-4 font-medium text-neutral-600">{{ $class->students_count }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('classes.show', $class) }}" class="font-semibold text-brand-600 hover:text-brand-700">Manage →</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6 grid gap-3 md:hidden">
        @foreach ($classes as $class)
            <a href="{{ route('classes.show', $class) }}" class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm transition active:bg-neutral-50">
                <div class="flex items-start justify-between gap-3">
                    <div><h2 class="font-bold text-neutral-900">{{ $class->name }}</h2><p class="mt-1 text-sm text-neutral-500">{{ $class->location ?: 'No location set' }}</p></div>
                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $class->type === 'regular' ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-700' }}">{{ ucfirst($class->type) }}</span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-neutral-100 pt-3 text-sm">
                    <div><span class="font-bold text-neutral-900">{{ $class->sessions_count }}</span> <span class="text-neutral-500">sessions</span></div>
                    <div><span class="font-bold text-neutral-900">{{ $class->students_count }}</span> <span class="text-neutral-500">students</span></div>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
