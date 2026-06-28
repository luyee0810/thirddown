@extends('layouts.parent')

@section('title', 'My children · Third Down Sports')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">My children</h1>
            <p class="mt-1 text-sm text-neutral-500">Add and manage the kids you’ve registered.</p>
        </div>
        <a href="{{ route('parent.children.create') }}"
            class="shrink-0 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
            + Add a child
        </a>
    </div>

    @if ($children->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
            <p class="text-sm text-neutral-500">You haven’t added any children yet.</p>
            <a href="{{ route('parent.children.create') }}" class="mt-3 inline-block text-sm font-semibold text-brand-600 hover:text-brand-700">
                Add your first child →
            </a>
        </div>
    @else
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($children as $child)
                <a href="{{ route('parent.children.edit', $child) }}"
                    class="group flex items-center gap-4 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm transition hover:border-brand-300 hover:shadow-md">
                    <x-student-avatar :student="$child" size="h-14 w-14" />
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-base font-bold text-neutral-900">{{ $child->full_name }}</h2>
                        <p class="text-xs text-neutral-500">
                            {{ $child->date_of_birth ? 'Age '.$child->date_of_birth->age : 'Age not set' }}
                            <span class="mx-1">·</span>
                            {{ $child->classes_count }} {{ Str::plural('class', $child->classes_count) }}
                        </p>
                    </div>
                    <span class="text-neutral-300 transition group-hover:text-brand-500" aria-hidden="true">›</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection
