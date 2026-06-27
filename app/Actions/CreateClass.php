<?php

namespace App\Actions;

use App\Models\TrainingClass;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class CreateClass
{
    /**
     * Create a class and generate its session(s).
     *
     * @param  array<string, mixed>  $data  Validated payload from StoreClassRequest.
     */
    public function execute(array $data, int $coachId): TrainingClass
    {
        return DB::transaction(function () use ($data, $coachId) {
            $isRegular = $data['type'] === 'regular';

            $class = TrainingClass::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'type' => $data['type'],
                'coach_id' => $coachId,
                'start_date' => $isRegular ? $data['start_date'] : ($data['session_date'] ?? null),
                'end_date' => $isRegular ? $data['end_date'] : ($data['session_date'] ?? null),
                'status' => 'active',
            ]);

            $dates = $isRegular
                ? $this->recurringDates($data['start_date'], $data['end_date'], $data['weekdays'])
                : [$data['session_date']];

            foreach ($dates as $date) {
                $class->sessions()->create([
                    'session_date' => $date,
                    'start_time' => $data['start_time'] ?? null,
                    'end_time' => $data['end_time'] ?? null,
                    'status' => 'scheduled',
                ]);
            }

            return $class;
        });
    }

    /**
     * Dates matching the chosen weekdays within the range.
     *
     * @param  array<int>  $weekdays  0 = Sunday … 6 = Saturday
     * @return array<string>
     */
    private function recurringDates(string $start, string $end, array $weekdays): array
    {
        $weekdays = array_map('intval', $weekdays);
        $dates = [];

        foreach (CarbonPeriod::create($start, $end) as $day) {
            if (in_array($day->dayOfWeek, $weekdays, true)) {
                $dates[] = $day->toDateString();
            }
            if (count($dates) >= 365) {
                break; // safety cap
            }
        }

        return $dates;
    }
}
