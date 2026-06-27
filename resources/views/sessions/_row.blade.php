{{-- @param \App\Models\ClassSession $session --}}
<li class="group flex flex-col gap-3 px-4 py-4 transition hover:bg-neutral-50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
    <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
            <span class="font-semibold text-neutral-900">{{ $session->trainingClass->name }}</span>
            @if ($session->attendances_count)
                <span class="inline-flex shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Completed</span>
            @else
                <span class="inline-flex shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-700">Upcoming</span>
            @endif
        </div>
        <div class="mt-1 flex flex-wrap gap-x-2 text-sm text-neutral-500">
            <span>{{ $session->session_date->format('D, j M Y') }}</span>
            @if ($session->start_time)
                <span aria-hidden="true">·</span>
                <span>{{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}@if ($session->end_time) – {{ \Carbon\Carbon::parse($session->end_time)->format('g:i A') }}@endif</span>
            @endif
        </div>
    </div>
    <a href="{{ route('attendance.edit', $session) }}"
        class="inline-flex min-h-10 shrink-0 items-center justify-center self-start rounded-lg border px-3.5 py-2 text-sm font-bold transition sm:self-auto {{ $session->attendances_count ? 'border-neutral-200 bg-white text-neutral-700 hover:border-brand-300 hover:text-brand-700' : 'border-brand-500 bg-brand-500 text-white hover:bg-brand-600' }}">
        {{ $session->attendances_count ? 'Review attendance' : 'Mark attendance' }}
    </a>
</li>
