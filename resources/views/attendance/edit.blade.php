@extends('layouts.coach')

@section('title', 'Attendance · '.$class->name)

@php
    $players = $students->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->full_name,
        'age' => $s->age,
        'photo' => $s->photo_url,
        'credits' => $s->credits,
        // No status yet means unmarked — the coach chooses, nothing is assumed.
        'status' => $existing[$s->id] ?? 'unmarked',
        // Already-saved players cannot go back to unmarked: the credit is spent.
        'saved' => isset($existing[$s->id]),
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
                    <span class="mx-1">·</span> Choose a status for each player. Present and late use one credit; absent and excused are free.
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
                    <div class="grid grid-cols-3 gap-5 sm:flex sm:gap-12">
                        <template x-for="status in statuses" :key="status">
                            <div>
                                <div class="text-xl font-bold sm:text-2xl" :class="meta[status].text" x-text="count(status)"></div>
                                <div class="text-xs font-medium capitalize text-neutral-500" x-text="status"></div>
                            </div>
                        </template>
                        <div>
                            <div class="text-xl font-bold text-neutral-400 sm:text-2xl" x-text="count('unmarked')"></div>
                            <div class="text-xs font-medium text-neutral-500">unmarked</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 self-start sm:self-auto">
                        <button type="button" @click="markAllPresent()" class="min-h-10 cursor-pointer rounded-lg px-3 py-2 text-sm font-bold text-emerald-700 transition hover:bg-white/70">Mark all present</button>
                        <button type="button" @click="clearAll()" x-show="players.some(p => ! p.saved && p.status !== 'unmarked')" x-cloak
                            class="min-h-10 cursor-pointer rounded-lg px-3 py-2 text-sm font-bold text-neutral-500 transition hover:bg-white/70">Clear unsaved</button>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    <template x-for="(p, index) in players" :key="p.id">
                        <div class="flex min-h-64 flex-col items-center rounded-2xl border bg-white px-4 py-5 text-center transition"
                            :class="meta[p.status].card">
                            {{-- Unmarked players submit nothing, so no record and no credit charge. --}}
                            <input type="hidden" :name="`attendance[${p.id}]`" :value="p.status" :disabled="p.status === 'unmarked'">
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
                                    <p class="mt-1.5">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                            :class="p.credits > 0 ? 'bg-emerald-50 text-emerald-700' : (p.credits === 0 ? 'bg-neutral-100 text-neutral-600' : 'bg-red-50 text-red-700')"
                                            x-text="`${p.credits} credits`"></span>
                                    </p>
                                </div>
                                <button type="button" @click="nextStatus(p)"
                                    class="mt-5 min-h-12 w-full rounded-xl border px-3 py-2 text-sm font-bold capitalize transition"
                                    :class="meta[p.status].active"
                                    x-text="`${meta[p.status].icon} ${meta[p.status].label}`"></button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <div class="sticky bottom-[4.75rem] z-20 mt-10 flex items-center justify-between gap-4 border-t border-neutral-100 bg-[#f8f8f7]/95 py-6 backdrop-blur md:bottom-0">
                <a href="{{ route('classes.show', $class) }}" class="px-3 py-2 text-sm font-semibold text-neutral-500 hover:text-neutral-800">Cancel</a>
                <div class="flex items-center gap-3">
                    <span class="hidden text-xs text-neutral-500 sm:block"><strong class="text-neutral-800" x-text="markedCount()"></strong> of <span x-text="players.length"></span> players marked</span>
                    <button type="submit" :disabled="markedCount() === 0"
                        class="min-h-11 cursor-pointer rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50 sm:px-7">Save attendance</button>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
    function attendance(players) {
        return {
            players,
            // Counters across the top; 'unmarked' is tracked separately.
            statuses: ['present', 'absent', 'late', 'excused'],
            // Tap order — every player starts unmarked and returns there.
            cycle: ['unmarked', 'present', 'absent', 'late', 'excused'],
            meta: {
                unmarked: { text: 'text-neutral-400', icon: '＋', label: 'Tap to mark', active: 'border-dashed border-neutral-300 bg-white text-neutral-500 hover:bg-neutral-50', card: 'border-dashed border-neutral-300' },
                present: { text: 'text-neutral-900', icon: '✓', label: 'present', active: 'border-emerald-300 bg-emerald-50 text-emerald-700 shadow-sm hover:bg-emerald-100', card: 'border-emerald-200' },
                absent: { text: 'text-neutral-900', icon: '×', label: 'absent', active: 'border-rose-300 bg-rose-50 text-rose-700 shadow-sm hover:bg-rose-100', card: 'border-rose-200' },
                late: { text: 'text-neutral-900', icon: '•', label: 'late', active: 'border-amber-300 bg-amber-50 text-amber-700 shadow-sm hover:bg-amber-100', card: 'border-amber-200' },
                excused: { text: 'text-neutral-900', icon: '−', label: 'excused', active: 'border-neutral-300 bg-neutral-100 text-neutral-700 shadow-sm hover:bg-neutral-200', card: 'border-neutral-200' },
            },
            setStatus(player, status) { player.status = status; },
            options(player) { return player.saved ? this.statuses : this.cycle; },
            nextStatus(player) {
                const options = this.options(player);
                const current = options.indexOf(player.status);
                player.status = options[(current + 1) % options.length];
            },
            markAllPresent() { this.players.forEach(player => player.status = 'present'); },
            clearAll() { this.players.forEach(player => { if (! player.saved) player.status = 'unmarked'; }); },
            count(status) { return this.players.filter(player => player.status === status).length; },
            markedCount() { return this.players.filter(player => player.status !== 'unmarked').length; },
        };
    }
</script>
@endsection
