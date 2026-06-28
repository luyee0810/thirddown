@extends('layouts.coach')

@section('title', 'Attendance · '.$class->name)

@php
    $players = $students->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->full_name,
        'age' => $s->date_of_birth?->age,
        'photo' => $s->photo_url,
        'status' => $existing[$s->id] ?? 'present',
    ])->values();
@endphp

@section('content')
<div x-data="attendance(@js($players))" class="mx-auto max-w-7xl">
    <form method="POST" action="{{ route('attendance.update', $session) }}">
        @csrf

        <div class="flex items-start justify-between gap-4">
            <div>
                <a href="{{ route('classes.show', $class) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-neutral-500 hover:text-neutral-800">← Back to class</a>
                <p class="mt-5 text-sm font-bold text-brand-600">{{ $session->session_date->format('l, j F Y') }}</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{{ $class->name }}</h1>
                <p class="mt-1 text-sm text-neutral-500">
                    @if ($session->start_time)
                        {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}@if ($session->end_time) – {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}@endif
                    @else
                        Time not set
                    @endif
                    <span class="mx-1">·</span> Choose a status for each player.
                </p>
            </div>
            <a href="{{ route('classes.show', $class) }}" aria-label="Close attendance" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-500 shadow-sm transition hover:bg-neutral-100">✕</a>
        </div>

        @if ($students->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center text-sm text-neutral-500">
                No students enrolled in this class yet.
                <a href="{{ route('classes.show', $class) }}" class="font-semibold text-brand-600">Assign students →</a>
            </div>
        @else
            <section class="mt-6">
                <div class="flex flex-col gap-4 rounded-2xl bg-neutral-100/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                    <div class="grid grid-cols-4 gap-5 sm:flex sm:gap-12">
                        <template x-for="status in statuses" :key="status">
                            <div>
                                <div class="text-xl font-bold sm:text-2xl" :class="meta[status].text" x-text="count(status)"></div>
                                <div class="text-xs font-medium capitalize text-neutral-500" x-text="status"></div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="markAllPresent()" class="min-h-10 self-start rounded-lg px-3 py-2 text-sm font-bold text-emerald-700 transition hover:bg-white/70 sm:self-auto">Mark all present</button>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    <template x-for="(p, index) in players" :key="p.id">
                        <div class="flex min-h-64 flex-col items-center rounded-2xl border bg-white px-4 py-5 text-center transition"
                            :class="meta[p.status].card">
                            <input type="hidden" :name="`attendance[${p.id}]`" :value="p.status">
                            <div class="h-20 w-20 overflow-hidden rounded-full bg-neutral-100">
                                <template x-if="p.photo">
                                    <img :src="p.photo" :alt="p.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!p.photo">
                                    <span class="flex h-full w-full items-center justify-center text-neutral-400">
                                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.42 0-8 2.69-8 6v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1c0-3.31-3.58-6-8-6Z"/>
                                        </svg>
                                    </span>
                                </template>
                            </div>
                            <div class="mt-4 flex min-w-0 flex-1 flex-col justify-between self-stretch">
                                <div>
                                    <h2 class="line-clamp-2 text-base font-bold leading-tight text-neutral-900" x-text="p.name"></h2>
                                    <p class="mt-1 text-sm text-neutral-500" x-text="p.age ? `Age ${p.age}` : 'Age not provided'"></p>
                                </div>
                                <button type="button" @click="nextStatus(p)"
                                    class="mt-5 min-h-12 w-full rounded-xl border px-3 py-2 text-sm font-bold capitalize transition"
                                    :class="meta[p.status].active"
                                    x-text="`${meta[p.status].icon} ${p.status}`"></button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <div class="sticky bottom-[4.75rem] z-20 mt-10 flex items-center justify-between gap-4 border-t border-neutral-100 bg-[#f8f8f7]/95 py-6 backdrop-blur md:bottom-0">
                <a href="{{ route('classes.show', $class) }}" class="px-3 py-2 text-sm font-semibold text-neutral-500 hover:text-neutral-800">Cancel</a>
                <div class="flex items-center gap-3">
                    <span class="hidden text-xs text-neutral-500 sm:block"><strong class="text-neutral-800" x-text="players.length"></strong> players marked</span>
                    <button type="submit" class="min-h-11 rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-600 sm:px-7">Save attendance</button>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
    function attendance(players) {
        return {
            players,
            statuses: ['present', 'absent', 'late', 'excused'],
            meta: {
                present: { text: 'text-neutral-900', icon: '✓', active: 'border-emerald-300 bg-emerald-50 text-emerald-700 shadow-sm hover:bg-emerald-100', card: 'border-emerald-200' },
                absent: { text: 'text-neutral-900', icon: '×', active: 'border-rose-300 bg-rose-50 text-rose-700 shadow-sm hover:bg-rose-100', card: 'border-rose-200' },
                late: { text: 'text-neutral-900', icon: '•', active: 'border-amber-300 bg-amber-50 text-amber-700 shadow-sm hover:bg-amber-100', card: 'border-amber-200' },
                excused: { text: 'text-neutral-900', icon: '−', active: 'border-neutral-300 bg-neutral-100 text-neutral-700 shadow-sm hover:bg-neutral-200', card: 'border-neutral-200' },
            },
            setStatus(player, status) { player.status = status; },
            nextStatus(player) {
                const current = this.statuses.indexOf(player.status);
                player.status = this.statuses[(current + 1) % this.statuses.length];
            },
            markAllPresent() { this.players.forEach(player => player.status = 'present'); },
            count(status) { return this.players.filter(player => player.status === status).length; },
        };
    }
</script>
@endsection
