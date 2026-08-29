@extends('layouts.coach')

@section('title', 'Edit '.$student->full_name.' · thirddown')

@section('content')
    <a href="{{ route('students.show', $student) }}" class="cursor-pointer text-sm text-neutral-500 hover:text-neutral-700">← {{ $student->full_name }}</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">Edit student</h1>

    <form method="POST" action="{{ route('students.update', $student) }}" class="mt-6 max-w-2xl space-y-6">
        @csrf
        @method('PUT')
        @include('students._form', ['student' => $student])

        <div class="flex items-center gap-3">
            <button type="submit"
                class="cursor-pointer rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
                Save changes
            </button>
            <a href="{{ route('students.show', $student) }}" class="cursor-pointer text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>
@endsection
