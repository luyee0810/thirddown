@extends('layouts.coach')

@section('title', 'New student · thirddown')

@section('content')
    <a href="{{ route('students.index') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← Students</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">Add a student</h1>

    <form method="POST" action="{{ route('students.store') }}" class="mt-6 max-w-2xl space-y-6">
        @csrf

        <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-neutral-900">Student details</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">First name</label>
                    <input name="first_name" value="{{ old('first_name') }}" required
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Last name</label>
                    <input name="last_name" value="{{ old('last_name') }}" required
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Date of birth <span class="text-neutral-400">(optional)</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Gender <span class="text-neutral-400">(optional)</span></label>
                    <input name="gender" value="{{ old('gender') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>
        </div>

        <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-neutral-900">Parent / guardian <span class="font-normal text-neutral-400">(optional)</span></h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Name</label>
                    <input name="parent_name" value="{{ old('parent_name') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Phone</label>
                    <input name="parent_phone" value="{{ old('parent_phone') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-neutral-700">Email</label>
                    <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700">Notes <span class="text-neutral-400">(optional)</span></label>
            <textarea name="notes" rows="2"
                class="mt-1.5 block w-full max-w-2xl rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
                Save student
            </button>
            <a href="{{ route('students.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>
@endsection
