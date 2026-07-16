@extends('layouts.admin')

@section('title', 'New user · Third Down Sports')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="cursor-pointer text-sm text-neutral-500 hover:text-neutral-700">← Users</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">Add a user</h1>
    <p class="mt-1 text-sm text-neutral-500">Create a coach, parent or administrator account.</p>

    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6 max-w-2xl space-y-6">
        @csrf
        @include('admin.users._form', ['user' => null, 'role' => $role])

        <div class="flex items-center gap-3">
            <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                Create user
            </button>
            <a href="{{ route('admin.users.index') }}" class="cursor-pointer text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>
@endsection
