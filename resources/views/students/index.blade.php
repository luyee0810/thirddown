@extends('layouts.coach')

@section('title', 'Students · Third Down Sports')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div><p class="text-sm font-semibold text-brand-600">Roster</p><h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Students</h1><p class="mt-1 text-sm text-neutral-500">Manage players and guardian details.</p></div>
    <a href="{{ route('students.create') }}" class="inline-flex min-h-11 shrink-0 items-center rounded-xl bg-brand-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-600">+ New student</a>
</div>

@if ($students->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center"><p class="text-sm text-neutral-500">No students yet.</p><a href="{{ route('students.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-600">Add your first student →</a></div>
@else
    <div class="mt-6 hidden overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm md:block">
        <table class="w-full text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50/80 text-left text-xs uppercase tracking-wider text-neutral-500"><tr><th class="px-6 py-4 font-semibold">Name</th><th class="px-5 py-4 font-semibold">Parent / guardian</th><th class="px-5 py-4 font-semibold">Classes</th><th class="px-6 py-4"></th></tr></thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($students as $student)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-6 py-4"><a href="{{ route('students.show', $student) }}" class="flex items-center gap-3"><x-student-avatar :student="$student" size="h-10 w-10" /><span class="font-semibold text-neutral-900">{{ $student->full_name }}</span></a></td>
                        <td class="px-5 py-4 text-neutral-600">{{ $student->parent_name ?: 'Not provided' }}@if ($student->parent_phone)<div class="mt-0.5 text-xs text-neutral-400">{{ $student->parent_phone }}</div>@endif</td>
                        <td class="px-5 py-4 font-medium text-neutral-600">{{ $student->classes_count }}</td>
                        <td class="px-6 py-4 text-right"><a href="{{ route('students.show', $student) }}" class="font-semibold text-brand-600">View →</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6 grid gap-3 md:hidden">
        @foreach ($students as $student)
            <a href="{{ route('students.show', $student) }}" class="flex items-center gap-3 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm">
                <x-student-avatar :student="$student" size="h-12 w-12" />
                <span class="min-w-0 flex-1"><strong class="block truncate text-sm text-neutral-900">{{ $student->full_name }}</strong><span class="mt-0.5 block truncate text-xs text-neutral-500">{{ $student->parent_name ?: 'No guardian details' }}</span></span>
                <span class="text-right"><strong class="block text-sm">{{ $student->classes_count }}</strong><span class="text-xs text-neutral-500">classes</span></span>
                <span class="text-neutral-300">→</span>
            </a>
        @endforeach
    </div>
@endif
@endsection
