{{-- Expects: $action (url), $method ('POST'|'PUT'), $child (Student|null), $submitLabel --}}
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mt-6 max-w-2xl space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
        <h3 class="text-sm font-semibold text-neutral-900">Child details</h3>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-neutral-700">First name</label>
                <input name="first_name" value="{{ old('first_name', $child?->first_name) }}" required
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700">Last name</label>
                <input name="last_name" value="{{ old('last_name', $child?->last_name) }}" required
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700">Date of birth <span class="text-neutral-400">(optional)</span></label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $child?->date_of_birth?->format('Y-m-d')) }}"
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700">Gender <span class="text-neutral-400">(optional)</span></label>
                <input name="gender" value="{{ old('gender', $child?->gender) }}"
                    class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral-700">Photo <span class="text-neutral-400">(optional)</span></label>
            <div class="mt-1.5 flex items-center gap-4">
                @if ($child)
                    <x-student-avatar :student="$child" size="h-14 w-14" />
                @endif
                <input type="file" name="photo" accept="image/*"
                    class="block w-full text-sm text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
            </div>
            <p class="mt-1 text-xs text-neutral-400">JPG or PNG, up to 4 MB.</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-neutral-700">Notes <span class="text-neutral-400">(optional)</span></label>
        <textarea name="notes" rows="2"
            class="mt-1.5 block w-full max-w-2xl rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/30"
            placeholder="Allergies, medical info, anything the coach should know.">{{ old('notes', $child?->notes) }}</textarea>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit"
            class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-600">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('parent.dashboard') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700">Cancel</a>
    </div>
</form>
