@extends('layouts.coach')

@section('title', $class->name.' · thirddown')

@section('content')
    <a href="{{ route('classes.index') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← Classes</a>

    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">{{ $class->name }}</h1>
            <p class="mt-1 text-sm text-neutral-500">
                <span class="font-medium text-brand-600">{{ ucfirst($class->type) }}</span>
                @if ($class->location) · {{ $class->location }} @endif
                · {{ $class->sessions->count() }} session(s)
            </p>
            @if ($class->description)<p class="mt-2 max-w-xl text-sm text-neutral-600">{{ $class->description }}</p>@endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">

        {{-- Enrolled students + assign --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-5">
            <h2 class="font-semibold">Students <span class="text-neutral-400">({{ $class->students->count() }})</span></h2>

            @if ($class->students->isEmpty())
                <p class="mt-3 text-sm text-neutral-500">No students enrolled yet.</p>
            @else
                <ul class="mt-3 divide-y divide-neutral-100">
                    @foreach ($class->students as $student)
                        <li class="flex items-center justify-between py-2.5">
                            <a href="{{ route('students.show', $student) }}" class="text-sm text-neutral-800 hover:text-brand-700">{{ $student->full_name }}</a>
                            <form method="POST" action="{{ route('classes.students.destroy', [$class, $student]) }}"
                                onsubmit="return confirm('Remove {{ $student->full_name }} from this class?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-medium text-neutral-400 hover:text-red-600">Remove</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Assign form --}}
            <div class="mt-5 border-t border-neutral-100 pt-5">
                <h3 class="text-sm font-semibold text-neutral-900">Assign students</h3>
                @if ($available->isEmpty())
                    <p class="mt-2 text-sm text-neutral-500">
                        All active students are enrolled.
                        <a href="{{ route('students.create') }}" class="font-medium text-brand-600 hover:underline">Add a new student →</a>
                    </p>
                @else
                    <form method="POST" action="{{ route('classes.students.store', $class) }}" class="mt-3"
                        x-data="{ open: false }">
                        @csrf
                        <div class="max-h-56 space-y-1 overflow-y-auto rounded-lg border border-neutral-200 p-2">
                            @foreach ($available as $student)
                                <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-neutral-50">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        class="h-4 w-4 rounded border-neutral-300 text-brand-600 focus:ring-brand-500/40">
                                    {{ $student->full_name }}
                                </label>
                            @endforeach
                        </div>
                        <button type="submit"
                            class="mt-3 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-600">
                            Enroll selected
                        </button>
                    </form>
                @endif
            </div>
        </section>

        {{-- Sessions --}}
        <section class="rounded-xl border border-neutral-200 bg-white p-5">
            <h2 class="font-semibold">Sessions <span class="text-neutral-400">({{ $class->sessions->count() }})</span></h2>
            <ul class="mt-3 divide-y divide-neutral-100">
                @foreach ($class->sessions as $session)
                    <li class="flex items-center justify-between gap-3 py-2.5 text-sm">
                        <div>
                            <div class="text-neutral-800">{{ $session->session_date->format('D, d M Y') }}</div>
                            <div class="text-xs text-neutral-500">
                                @if ($session->start_time)
                                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}@if ($session->end_time) – {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}@endif
                                @else
                                    No time set
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('attendance.edit', $session) }}"
                            class="shrink-0 rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-semibold text-neutral-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                            @if ($session->attendances_count) ✓ Attendance @else Mark attendance @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
@endsection
