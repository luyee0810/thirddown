@props(['age' => null])

{{--
    Read-only age display. The server renders the current age; the script keeps it
    in sync while the coach/parent edits the date of birth in the same form.
--}}
<div>
    <label class="block text-sm font-medium text-neutral-700">Age</label>
    <p data-age-display
        class="mt-1.5 flex min-h-[2.875rem] w-full items-center rounded-lg border border-dashed border-neutral-300 bg-neutral-50 px-3 py-2.5 text-sm text-neutral-600">
        {{ $age ?? '—' }}
    </p>
    <p class="mt-1 text-xs text-neutral-400">Calculated automatically from the date of birth.</p>
</div>

@once
    <script>
        (function () {
            function ageFrom(value) {
                if (! value) return null;
                var parts = value.split('-').map(Number);
                if (parts.length !== 3 || parts.some(isNaN)) return null;
                var today = new Date();
                var age = today.getFullYear() - parts[0];
                var monthDiff = (today.getMonth() + 1) - parts[1];
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < parts[2])) age--;
                return age >= 0 && age < 150 ? age : null;
            }

            function refresh(input) {
                var scope = input.closest('form') || document;
                var display = scope.querySelector('[data-age-display]');
                if (! display) return;
                var age = ageFrom(input.value);
                display.textContent = age === null ? '—' : String(age);
            }

            document.addEventListener('input', function (event) {
                if (event.target.matches('input[name="date_of_birth"]')) refresh(event.target);
            });
            document.addEventListener('change', function (event) {
                if (event.target.matches('input[name="date_of_birth"]')) refresh(event.target);
            });
        })();
    </script>
@endonce
