@extends('layouts.coach')

@section('title', $student->full_name.' · Third Down Sports')

@section('content')
    <a href="{{ route('students.index') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← Students</a>

    {{-- Student header --}}
    <div class="mt-3 flex items-center gap-4">
        <x-student-avatar :student="$student" size="h-16 w-16" />
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">{{ $student->full_name }}</h1>
            <p class="mt-0.5 text-sm text-neutral-500">
                @if ($student->date_of_birth) Age {{ $student->date_of_birth->age }} @endif
                @if ($student->parent_name) · Parent: {{ $student->parent_name }}@if ($student->parent_phone) ({{ $student->parent_phone }})@endif @endif
            </p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">

        {{-- Current classes --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-5">
            <h2 class="font-semibold">Enrolled classes <span class="text-neutral-400">({{ $enrolled->count() }})</span></h2>

            @if ($enrolled->isEmpty())
                <p class="mt-3 text-sm text-neutral-500">Not in any of your classes yet.</p>
            @else
                <ul class="mt-3 divide-y divide-neutral-100">
                    @foreach ($enrolled as $class)
                        <li class="flex items-center justify-between py-2.5">
                            <a href="{{ route('classes.show', $class) }}" class="text-sm font-medium text-neutral-800 hover:text-brand-700">
                                {{ $class->name }}
                                <span class="ml-1 inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $class->type === 'regular' ? 'bg-brand-50 text-brand-700' : 'bg-neutral-100 text-neutral-600' }}">{{ ucfirst($class->type) }}</span>
                            </a>
                            <form method="POST" action="{{ route('students.classes.destroy', [$student, $class]) }}"
                                onsubmit="return confirm('Remove {{ $student->full_name }} from {{ $class->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-medium text-neutral-400 hover:text-red-600">Remove</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Add to multiple classes --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-5">
            <h2 class="font-semibold">Add to classes</h2>
            @if ($available->isEmpty())
                <p class="mt-3 text-sm text-neutral-500">
                    {{ $student->full_name }} is already in all of your classes.
                    <a href="{{ route('classes.create') }}" class="font-medium text-brand-600 hover:underline">Create a class →</a>
                </p>
            @else
                <form method="POST" action="{{ route('students.classes.store', $student) }}" class="mt-3"
                    x-data="{ count: 0 }">
                    @csrf
                    <p class="mb-2 text-sm text-neutral-500">Tick every class to enrol {{ $student->first_name }} into, then save once.</p>
                    <div class="max-h-72 space-y-1 overflow-y-auto rounded-lg border border-neutral-200 p-2">
                        @foreach ($available as $class)
                            <label class="flex cursor-pointer items-center justify-between gap-2 rounded-md px-2 py-2 text-sm hover:bg-neutral-50">
                                <span>
                                    <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                                        @change="count += $event.target.checked ? 1 : -1"
                                        class="mr-2 h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-500/40">
                                    {{ $class->name }}
                                </span>
                                <span class="text-xs text-neutral-400">{{ ucfirst($class->type) }} · {{ $class->sessions_count }} sessions</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" :disabled="count === 0"
                        class="mt-3 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50">
                        <span x-show="count === 0">Select classes</span>
                        <span x-show="count > 0" x-cloak>Add to <span x-text="count"></span> class<span x-show="count > 1">es</span></span>
                    </button>
                </form>
            @endif
        </section>
    </div>
@endsection
