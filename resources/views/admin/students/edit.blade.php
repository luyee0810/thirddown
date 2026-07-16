@extends('layouts.admin')

@section('title', 'Edit student · Third Down Sports')

@section('content')
    <a href="{{ route('admin.students.index') }}" class="cursor-pointer text-sm text-neutral-500 hover:text-neutral-700">← Students</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">{{ $student->full_name }}</h1>

    <form method="POST" action="{{ route('admin.students.update', $student) }}" class="mt-6 max-w-2xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.students._form', ['student' => $student])

        <div class="flex items-center gap-3">
            <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                Save changes
            </button>
            <a href="{{ route('admin.students.index') }}" class="cursor-pointer text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>

    <div class="mt-8 max-w-2xl rounded-xl border border-red-200 bg-red-50/60 p-5">
        <h2 class="text-sm font-semibold text-red-800">Delete this student</h2>
        <p class="mt-1 text-sm text-red-700">Removes the student along with their enrollments and attendance records.</p>
        <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="mt-3"
            onsubmit="return confirm('Delete {{ addslashes($student->full_name) }}? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="cursor-pointer rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-600 hover:text-white">
                Delete student
            </button>
        </form>
    </div>
@endsection
