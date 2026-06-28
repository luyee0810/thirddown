@extends('layouts.parent')

@section('title', 'Add a child · Third Down Sports')

@section('content')
    <a href="{{ route('parent.dashboard') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← My children</a>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">Add a child</h1>
    <p class="mt-1 text-sm text-neutral-500">Fill in your child’s details. You can add more than one.</p>

    @include('parent.children._form', [
        'action' => route('parent.children.store'),
        'method' => 'POST',
        'child' => null,
        'submitLabel' => 'Add child',
    ])
@endsection
