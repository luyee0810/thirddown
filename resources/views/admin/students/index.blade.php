@extends('layouts.admin')

@section('title', 'Students · Third Down Sports')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold text-slate-500">Roster</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Students</h1>
        <p class="mt-1 text-sm text-neutral-500">Every student across the site.</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="inline-flex min-h-11 shrink-0 cursor-pointer items-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800">+ New student</a>
</div>

@if ($students->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
        <p class="text-sm text-neutral-500">No students yet.</p>
    </div>
@else
    <div class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50/80 text-left text-xs uppercase tracking-wider text-neutral-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="hidden px-5 py-4 font-semibold sm:table-cell">Guardian</th>
                    <th class="hidden px-5 py-4 font-semibold md:table-cell">Parent account</th>
                    <th class="px-5 py-4 font-semibold">Classes</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($students as $student)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.students.edit', $student) }}" class="flex items-center gap-3">
                                <x-student-avatar :student="$student" size="h-10 w-10" />
                                <span class="cursor-pointer font-semibold text-neutral-900 hover:text-slate-700">{{ $student->full_name }}</span>
                            </a>
                        </td>
                        <td class="hidden px-5 py-4 text-neutral-600 sm:table-cell">{{ $student->parent_name ?: 'Not provided' }}</td>
                        <td class="hidden px-5 py-4 text-neutral-600 md:table-cell">{{ $student->parent?->name ?? '—' }}</td>
                        <td class="px-5 py-4 font-medium text-neutral-600">{{ $student->classes_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.students.edit', $student) }}" class="cursor-pointer font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                                    onsubmit="return confirm('Delete {{ addslashes($student->full_name) }}? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cursor-pointer font-semibold text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
