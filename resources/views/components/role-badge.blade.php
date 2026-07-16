@props(['role'])

@php
    $styles = [
        'admin' => 'bg-slate-900 text-white',
        'coach' => 'bg-brand-50 text-brand-700',
        'parent' => 'bg-emerald-50 text-emerald-700',
    ];
    $class = $styles[$role] ?? 'bg-neutral-100 text-neutral-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold $class"]) }}>
    {{ ucfirst($role) }}
</span>
