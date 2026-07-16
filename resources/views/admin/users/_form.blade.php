@php
    $user = $user ?? null;
    $currentRole = old('role', $user->role ?? ($role ?? 'coach'));
    $isActive = old('is_active', $user->is_active ?? true);
@endphp

<div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
    <div>
        <label class="block text-sm font-medium text-neutral-700">Role</label>
        <div class="mt-2 grid grid-cols-3 gap-2">
            @foreach (['coach' => 'Coach', 'parent' => 'Parent', 'admin' => 'Admin'] as $value => $label)
                <label class="flex cursor-pointer items-center justify-center rounded-lg border px-3 py-2.5 text-sm font-semibold transition has-[:checked]:border-slate-900 has-[:checked]:bg-slate-900 has-[:checked]:text-white {{ 'border-neutral-300 text-neutral-600 hover:bg-neutral-50' }}">
                    <input type="radio" name="role" value="{{ $value }}" class="sr-only" @checked($currentRole === $value)>
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-neutral-700">Name</label>
            <input name="name" value="{{ old('name', $user->name ?? '') }}" required
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Phone <span class="text-neutral-400">(optional)</span></label>
            <input name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div class="flex items-end">
            <label class="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-neutral-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked($isActive)
                    class="h-4 w-4 cursor-pointer rounded border-neutral-300 text-slate-900 focus:ring-slate-500/30">
                Active account
            </label>
        </div>
    </div>
</div>

<div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
    <h3 class="text-sm font-semibold text-neutral-900">
        {{ $user ? 'Reset password' : 'Password' }}
        @if ($user)<span class="font-normal text-neutral-400">— leave blank to keep current</span>@endif
    </h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-neutral-700">{{ $user ? 'New password' : 'Password' }}</label>
            <input type="password" name="password" @required(! $user)
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Confirm password</label>
            <input type="password" name="password_confirmation" @required(! $user)
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
    </div>
</div>
