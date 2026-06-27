@props(['class' => 'h-9'])

<img src="{{ asset('images/logo.svg') }}" alt="Third Down Sports" {{ $attributes->merge(['class' => $class]) }}>
