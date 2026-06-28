@extends('layouts.app')

@section('title', 'Create a parent account · Third Down Sports')

@section('body')
<div class="min-h-full lg:grid lg:grid-cols-2">

    {{-- Brand panel --}}
    <div class="relative hidden bg-neutral-950 lg:flex lg:flex-col lg:justify-between lg:p-12">
        <div class="flex items-center gap-3 text-white">
            <span class="inline-flex items-center rounded-xl bg-white px-3 py-2">
                <x-app-logo class="h-9" />
            </span>
            <span class="text-lg font-semibold tracking-tight">Third Down Sports</span>
        </div>
        <div>
            <h2 class="max-w-sm text-3xl font-semibold leading-tight text-white">
                Register your kids. <span class="text-brand-500">Keep them on the field.</span>
            </h2>
            <p class="mt-4 max-w-sm text-sm text-neutral-400">
                Create a parent account to add your children and manage their details.
            </p>
        </div>
        <p class="text-xs text-neutral-600">&copy; {{ date('Y') }} Third Down Sports</p>
    </div>

    {{-- Form panel --}}
    <div class="flex min-h-full items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            {{-- Mobile brand --}}
            <div class="mb-8 flex items-center lg:hidden">
                <x-app-logo class="h-10" />
            </div>

            <h1 class="text-2xl font-semibold tracking-tight">Create a parent account</h1>
            <p class="mt-1 text-sm text-neutral-500">Already have one?
                <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>.
            </p>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-700">Your name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}"
                        required autofocus autocomplete="name"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-neutral-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        required autocomplete="email"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                        placeholder="you@example.com">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-neutral-700">Phone <span class="text-neutral-400">(optional)</span></label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                        autocomplete="tel"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-neutral-700">Password</label>
                    <input id="password" name="password" type="password"
                        required autocomplete="new-password"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                        placeholder="••••••••">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-neutral-700">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        required autocomplete="new-password"
                        class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/40">
                    Create account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
