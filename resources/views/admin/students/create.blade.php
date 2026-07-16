@extends('layouts.admin')

@section('title', 'New student · Third Down Sports')

@section('content')
    <a href="{{ route('admin.students.index') }}" class="cursor-pointer text-sm text-neutral-500 hover:text-neutral-700">← Students</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">Add a student</h1>

    <form method="POST" action="{{ route('admin.students.store') }}" class="mt-6 max-w-2xl space-y-6">
        @csrf
        @include('admin.students._form', ['student' => null])

        <div class="flex items-center gap-3">
            <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                Save student
            </button>
            <a href="{{ route('admin.students.index') }}" class="cursor-pointer text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>
@endsection
