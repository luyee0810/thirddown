@extends('layouts.parent')

@section('title', 'Edit '.$child->full_name.' · Third Down Sports')

@section('content')
    <a href="{{ route('parent.dashboard') }}" class="text-sm text-neutral-500 hover:text-neutral-700">← My children</a>
    <h1 class="mt-2 text-2xl font-bold tracking-tight">Edit {{ $child->first_name }}</h1>
    <p class="mt-1 text-sm text-neutral-500">Update your child’s details.</p>

    @include('parent.children._form', [
        'action' => route('parent.children.update', $child),
        'method' => 'PUT',
        'child' => $child,
        'submitLabel' => 'Save changes',
    ])
@endsection
