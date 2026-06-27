@props([
    'student',
    'size' => 'h-12 w-12',
])

@php
    // Deterministic tint for the fallback icon, per student.
    $tints = [
        'bg-rose-100 text-rose-500', 'bg-sky-100 text-sky-500', 'bg-amber-100 text-amber-500',
        'bg-emerald-100 text-emerald-500', 'bg-violet-100 text-violet-500', 'bg-orange-100 text-orange-500',
    ];
    $tint = $tints[$student->id % count($tints)];
@endphp

@if ($student->photo_url)
    <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}"
        {{ $attributes->merge(['class' => "$size rounded-full object-cover ring-2 ring-white"]) }}>
@else
    {{-- Default user icon placeholder until photos are uploaded. --}}
    <span {{ $attributes->merge(['class' => "$size $tint inline-flex items-center justify-center rounded-full ring-2 ring-white"]) }}>
        <svg class="h-1/2 w-1/2" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.42 0-8 2.69-8 6v1a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-1c0-3.31-3.58-6-8-6Z"/>
        </svg>
    </span>
@endif
