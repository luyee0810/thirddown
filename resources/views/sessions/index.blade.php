@extends('layouts.coach')

@section('title', 'Sessions · Third Down Sports')

@section('content')
<div><p class="text-sm font-semibold text-brand-600">Attendance</p><h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Sessions</h1><p class="mt-1 text-sm text-neutral-500">Choose a session to mark or review attendance.</p></div>

@if ($upcoming->isEmpty() && $past->isEmpty())
    <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-white p-12 text-center"><p class="text-sm text-neutral-500">No sessions yet.</p><a href="{{ route('classes.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-600">Create a class →</a></div>
@endif

@if ($upcoming->isNotEmpty())
    <section class="mt-7 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <div class="border-b border-neutral-100 px-4 py-4 sm:px-6"><h2 class="font-bold">Upcoming</h2><p class="mt-0.5 text-xs text-neutral-500">Sessions that still need your attention.</p></div>
        <ul class="divide-y divide-neutral-100">@foreach ($upcoming as $session) @include('sessions._row', ['session' => $session]) @endforeach</ul>
    </section>
@endif

@if ($past->isNotEmpty())
    <section class="mt-6 overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
        <div class="border-b border-neutral-100 px-4 py-4 sm:px-6"><h2 class="font-bold">Past sessions</h2><p class="mt-0.5 text-xs text-neutral-500">Completed sessions and attendance records.</p></div>
        <ul class="divide-y divide-neutral-100">@foreach ($past as $session) @include('sessions._row', ['session' => $session]) @endforeach</ul>
    </section>
@endif
@endsection
