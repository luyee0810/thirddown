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
            <section class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-neutral-100 bg-neutral-50/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="grid grid-cols-4 gap-5 sm:flex sm:gap-10">
                        <template x-for="status in statuses" :key="status">
                            <div>
                                <div class="text-xl font-bold sm:text-2xl" :class="meta[status].text" x-text="count(status)"></div>
                                <div class="text-[11px] font-medium capitalize text-neutral-500 sm:text-xs" x-text="status"></div>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="markAllPresent()" class="min-h-10 self-start rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100 sm:self-auto">Mark all present</button>
                </div>

                <div class="grid grid-cols-2 gap-3 p-3 sm:grid-cols-3 sm:p-4 lg:grid-cols-4 xl:grid-cols-5">
                    <template x-for="(p, index) in players" :key="p.id">
                        <div class="flex flex-col overflow-hidden rounded-xl border bg-white transition"
                            :class="meta[p.status].card">
                            <input type="hidden" :name="`attendance[${p.id}]`" :value="p.status">
                            <div class="relative aspect-square w-full bg-neutral-100">
                                <template x-if="p.photo">
                                    <img :src="p.photo" :alt="p.name" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!p.photo">
                                    <span class="flex h-full w-full items-center justify-center text-3xl font-bold text-neutral-300" x-text="index + 1"></span>
                                </template>
                                <span class="absolute right-2 top-2 rounded-full px-2 py-1 text-[10px] font-bold capitalize shadow-sm" :class="meta[p.status].pill" x-text="p.status"></span>
                            </div>
                            <div class="flex min-w-0 flex-1 flex-col justify-between p-2.5">
                                <div>
                                    <h2 class="line-clamp-2 text-sm font-bold leading-tight text-neutral-900" x-text="p.name"></h2>
                                    <p class="mt-1 text-xs text-neutral-500" x-text="p.age ? `Age ${p.age}` : 'Age not provided'"></p>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-1.5">
                                    <template x-for="status in statuses" :key="status">
                                        <button type="button" @click="setStatus(p, status)"
                                            class="min-h-9 min-w-0 rounded-lg border px-1.5 py-1 text-[11px] font-bold capitalize leading-tight transition"
                                            :class="p.status === status ? meta[status].active : 'border-neutral-200 bg-white text-neutral-500 hover:border-neutral-300 hover:bg-neutral-50'"
                                            :aria-pressed="p.status === status" x-text="status"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <div class="sticky bottom-[4.75rem] z-20 mt-5 flex items-center justify-between gap-4 rounded-2xl border border-neutral-200 bg-white/95 p-3 shadow-[0_12px_40px_rgba(0,0,0,.14)] backdrop-blur md:bottom-4 sm:p-4">
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
                present: { text: 'text-emerald-700', pill: 'bg-emerald-100 text-emerald-700', active: 'border-emerald-400 bg-emerald-50 text-emerald-700 shadow-sm', card: 'border-emerald-300 ring-1 ring-emerald-200' },
                absent: { text: 'text-rose-700', pill: 'bg-rose-100 text-rose-700', active: 'border-rose-400 bg-rose-50 text-rose-700 shadow-sm', card: 'border-rose-300 ring-1 ring-rose-200' },
                late: { text: 'text-amber-700', pill: 'bg-amber-100 text-amber-700', active: 'border-amber-400 bg-amber-50 text-amber-700 shadow-sm', card: 'border-amber-300 ring-1 ring-amber-200' },
                excused: { text: 'text-blue-700', pill: 'bg-blue-100 text-blue-700', active: 'border-blue-400 bg-blue-50 text-blue-700 shadow-sm', card: 'border-blue-300 ring-1 ring-blue-200' },
            },
            setStatus(player, status) { player.status = status; },
            markAllPresent() { this.players.forEach(player => player.status = 'present'); },
            count(status) { return this.players.filter(player => player.status === status).length; },
        };
    }
</script>
@endsection
