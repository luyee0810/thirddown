@php $student = $student ?? null; @endphp

<div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
    <h3 class="text-sm font-semibold text-neutral-900">Student details</h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-neutral-700">First name</label>
            <input name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" required
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Last name</label>
            <input name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" required
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Date of birth <span class="text-neutral-400">(optional)</span></label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($student->date_of_birth ?? null)->format('Y-m-d')) }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Gender <span class="text-neutral-400">(optional)</span></label>
            <input name="gender" value="{{ old('gender', $student->gender ?? '') }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
    </div>
</div>

<div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-5">
    <h3 class="text-sm font-semibold text-neutral-900">Parent / guardian <span class="font-normal text-neutral-400">(optional)</span></h3>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-neutral-700">Name</label>
            <input name="parent_name" value="{{ old('parent_name', $student->parent_name ?? '') }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div>
            <label class="block text-sm font-medium text-neutral-700">Phone</label>
            <input name="parent_phone" value="{{ old('parent_phone', $student->parent_phone ?? '') }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-neutral-700">Email</label>
            <input type="email" name="parent_email" value="{{ old('parent_email', $student->parent_email ?? '') }}"
                class="mt-1.5 block w-full rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
        </div>
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-neutral-700">Notes <span class="text-neutral-400">(optional)</span></label>
    <textarea name="notes" rows="2"
        class="mt-1.5 block w-full max-w-2xl rounded-lg border border-neutral-300 px-3 py-2.5 text-sm outline-none focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">{{ old('notes', $student->notes ?? '') }}</textarea>
</div>
