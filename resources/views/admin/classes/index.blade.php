@extends('layouts.admin')

@section('title', 'Classes · Third Down Sports')

@section('content')
<div class="flex items-end justify-between gap-4">
    <div>
        <p class="text-sm font-semibold text-slate-500">Programme</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Classes</h1>
        <p class="mt-1 text-sm text-neutral-500">Every class across all coaches.</p>
    </div>
</div>

@if ($classes->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center">
        <p class="text-sm text-neutral-500">No classes yet.</p>
    </div>
@else
    <div class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="border-b border-neutral-200 bg-neutral-50/80 text-left text-xs uppercase tracking-wider text-neutral-500">
                <tr>
                    <th class="px-6 py-4 font-semibold">Name</th>
                    <th class="hidden px-5 py-4 font-semibold sm:table-cell">Sessions</th>
                    <th class="hidden px-5 py-4 font-semibold sm:table-cell">Students</th>
                    <th class="px-5 py-4 font-semibold">Coach</th>
                    <th class="px-6 py-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($classes as $class)
                    <tr class="transition hover:bg-neutral-50">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-neutral-900">{{ $class->name }}</div>
                            @if ($class->location)<div class="mt-0.5 text-xs text-neutral-500">{{ $class->location }}</div>@endif
                        </td>
                        <td class="hidden px-5 py-4 font-medium text-neutral-600 sm:table-cell">{{ $class->sessions_count }}</td>
                        <td class="hidden px-5 py-4 font-medium text-neutral-600 sm:table-cell">{{ $class->students_count }}</td>
                        <td class="px-5 py-4">
                            @if ($coaches->count() > 1)
                                <form method="POST" action="{{ route('admin.classes.coach', $class) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="coach_id" onchange="this.form.submit()"
                                        class="cursor-pointer rounded-lg border border-neutral-300 px-2.5 py-1.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                                        @foreach ($coaches as $coach)
                                            <option value="{{ $coach->id }}" @selected($coach->id === $class->coach_id)>{{ $coach->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            @else
                                <span class="text-neutral-600">{{ $class->coach?->name ?? '—' }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                onsubmit="return confirm('Delete {{ addslashes($class->name) }}? This removes its sessions and attendance records.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cursor-pointer font-semibold text-red-600 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection
