@extends('layouts.admin')

@section('title', 'Edit user · Third Down Sports')

@section('content')
    <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="cursor-pointer text-sm text-neutral-500 hover:text-neutral-700">← Users</a>
    <div class="mt-2 flex items-center gap-3">
        <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
        <x-role-badge :role="$user->role" />
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 max-w-2xl space-y-6">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user])

        <div class="flex items-center gap-3">
            <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                Save changes
            </button>
            <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" class="cursor-pointer text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>

    {{-- Coach's classes: reassign before this coach can be deleted. --}}
    @if ($user->role === 'coach')
        <div class="mt-8 max-w-2xl">
            <h2 class="text-lg font-bold tracking-tight">Classes coached</h2>
            @if ($classes->isEmpty())
                <p class="mt-2 text-sm text-neutral-500">This coach has no classes. They can be deleted safely.</p>
            @else
                <p class="mt-1 text-sm text-neutral-500">Reassign these before deleting this coach.</p>

                @if ($coaches->isEmpty())
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        No other coaches exist to reassign to. Create another coach first.
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.users.reassign-classes', $user) }}"
                        class="mt-3 flex flex-wrap items-end gap-3 rounded-xl border border-neutral-200 bg-white p-4">
                        @csrf
                        @method('PUT')
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-neutral-700">Reassign all {{ $classes->count() }} {{ Str::plural('class', $classes->count()) }} to</label>
                            <select name="new_coach_id" required
                                class="mt-1.5 block w-full cursor-pointer rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                                @foreach ($coaches as $coach)
                                    <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="cursor-pointer rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Reassign
                        </button>
                    </form>
                @endif

                <ul class="mt-3 divide-y divide-neutral-100 overflow-hidden rounded-xl border border-neutral-200 bg-white">
                    @foreach ($classes as $class)
                        <li class="flex items-center justify-between px-4 py-3 text-sm">
                            <span class="font-semibold text-neutral-900">{{ $class->name }}</span>
                            <span class="text-neutral-500">{{ $class->sessions_count }} sessions · {{ $class->students_count }} students</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Danger zone --}}
    @if ($user->id !== auth()->id())
        <div class="mt-8 max-w-2xl rounded-xl border border-red-200 bg-red-50/60 p-5">
            <h2 class="text-sm font-semibold text-red-800">Delete this user</h2>
            <p class="mt-1 text-sm text-red-700">Permanently removes the account. This cannot be undone.</p>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3"
                onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cursor-pointer rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-600 hover:text-white">
                    Delete user
                </button>
            </form>
        </div>
    @endif
@endsection
