<?php

namespace Tests\Unit;

use App\Models\Student;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentAgeTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_age_is_null_without_a_date_of_birth(): void
    {
        $this->assertNull((new Student)->age);
    }

    public function test_age_is_derived_from_the_date_of_birth(): void
    {
        Carbon::setTestNow('2026-08-10');

        $this->assertSame(11, (new Student(['date_of_birth' => '2015-08-10']))->age);
        $this->assertSame(10, (new Student(['date_of_birth' => '2015-08-11']))->age);
    }

    public function test_age_increases_on_the_students_birthday(): void
    {
        $student = new Student(['date_of_birth' => '2015-09-01']);

        Carbon::setTestNow('2026-08-31');
        $this->assertSame(10, $student->age);

        // Same record, no write — the value moves with the calendar.
        Carbon::setTestNow('2026-09-01');
        $this->assertSame(11, $student->age);

        Carbon::setTestNow('2027-09-01');
        $this->assertSame(12, $student->age);
    }
}
