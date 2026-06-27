@extends('layouts.coach')

@section('title', 'New class · thirddown')

@section('content')
    <a href="{{ route('classes.index') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← Classes</a>
    <h1 class="mt-2 text-2xl font-semibold tracking-tight">Create a class</h1>

    <form method="POST" action="{{ route('classes.store') }}" class="mt-6 max-w-2xl space-y-6"
        x-data="{ type: '{{ old('type', 'single') }}' }">
        @csrf

        {{-- Type toggle --}}
        <div class="grid grid-cols-2 gap-3">
            <label class="cursor-pointer">
                <input type="radio" name="type" value="single" x-model="type" class="peer sr-only">
                <div class="rounded-xl border border-neutral-300 p-4 transition peer-checked:border-brand-500 peer-checked:bg-brand-50">
                    <div class="font-semibold">Single class</div>
                    <p class="mt-0.5 text-sm text-neutral-500">One session on a chosen date.</p>
                </div>
            </label>
            <label class="cursor-pointer">
                <input type="radio" name="type" value="regular" x-model="type" class="peer sr-only">
                <div class="rounded-xl border border-neutral-300 p-4 transition peer-checked:border-brand-500 peer-checked:bg-brand-50">
                    <div class="font-semibold">Regular class</div>
                    <p class="mt-0.5 text-sm text-neutral-500">Recurring weekly sessions.</p>
                </div>
            </label>
        </div>

        <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
            <div>
                <label class="block text-sm font-medium text-neutral-700">Class name</label>
                <input name="name" value="{{ old('name') }}" required
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                    placeholder="U12 Saturday Training">
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Location <span class="text-neutral-400">(optional)</span></label>
                    <input name="location" value="{{ old('location') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                        placeholder="Field 3">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700">Description <span class="text-neutral-400">(optional)</span></label>
                <textarea name="description" rows="2"
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Single-class fields --}}
        <div x-show="type === 'single'" x-cloak class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-neutral-900">Session</h3>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Date</label>
                    <input type="date" name="session_date" value="{{ old('session_date') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Start time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">End time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>
        </div>

        {{-- Regular-class fields --}}
        <div x-show="type === 'regular'" x-cloak class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-neutral-900">Recurrence</h3>
            <div>
                <label class="block text-sm font-medium text-neutral-700">Repeats on</label>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $i => $day)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="weekdays[]" value="{{ $i }}"
                                {{ in_array((string) $i, old('weekdays', []), true) ? 'checked' : '' }}
                                class="peer sr-only">
                            <span class="inline-flex rounded-lg border border-neutral-300 px-3 py-1.5 text-sm font-medium transition peer-checked:border-brand-500 peer-checked:bg-brand-500 peer-checked:text-white">
                                {{ $day }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Start date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">End date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-neutral-700">Start time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-700">End time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>
            <p class="text-xs text-neutral-500">A session will be generated for each matching weekday between the start and end dates.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
                Create class
            </button>
            <a href="{{ route('classes.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
        </div>
    </form>
@endsection
