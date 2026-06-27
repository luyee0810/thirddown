<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Malaysian given names by ethnicity and gender.
     *
     * @var array<string, array<string, array<string>>>
     */
    private array $given = [
        'malay' => [
            'male' => ['Aiman', 'Haziq', 'Irfan', 'Danial', 'Amir', 'Zikri', 'Farhan', 'Hakim', 'Iskandar', 'Adam'],
            'female' => ['Aisyah', 'Nabila', 'Farah', 'Hana', 'Alia', 'Sofea', 'Balqis', 'Iman', 'Yasmin', 'Nurin'],
        ],
        'chinese' => [
            'male' => ['Wei Jie', 'Jun Hao', 'Zhi Hao', 'Kai Xin', 'Yong Sheng', 'Chee Keong', 'Jia Hao', 'Boon Heng'],
            'female' => ['Mei Ling', 'Hui Ying', 'Xin Yi', 'Jia Wen', 'Li Wen', 'Pei Shan', 'Wan Ting', 'Shu Min'],
        ],
        'indian' => [
            'male' => ['Arjun', 'Kavin', 'Dinesh', 'Prakash', 'Vimal', 'Harish', 'Raj', 'Suresh'],
            'female' => ['Priya', 'Divya', 'Kavya', 'Anitha', 'Meena', 'Deepa', 'Shanti', 'Lakshmi'],
        ],
    ];

    /** @var array<string> */
    private array $malayFather = ['Abdullah', 'Razak', 'Ismail', 'Hassan', 'Ibrahim', 'Yusof', 'Karim', 'Rahman', 'Zainal'];

    /** @var array<string> */
    private array $chineseSurname = ['Tan', 'Lim', 'Lee', 'Wong', 'Ng', 'Chong', 'Goh', 'Teoh', 'Ong', 'Yeoh', 'Cheah'];

    /** @var array<string> */
    private array $indianFather = ['Muthu', 'Ramasamy', 'Kumar', 'Subramaniam', 'Krishnan', 'Rajan', 'Suppiah'];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ethnicity = $this->faker->randomElement(['malay', 'chinese', 'indian']);
        $gender = $this->faker->randomElement(['male', 'female']);
        $first = $this->faker->randomElement($this->given[$ethnicity][$gender]);

        $last = match ($ethnicity) {
            'malay' => ($gender === 'male' ? 'bin ' : 'binti ').$this->faker->randomElement($this->malayFather),
            'chinese' => $this->faker->randomElement($this->chineseSurname),
            'indian' => ($gender === 'male' ? 'a/l ' : 'a/p ').$this->faker->randomElement($this->indianFather),
        };

        $hasParent = $this->faker->boolean(80);

        return [
            'first_name' => $first,
            'last_name' => $last,
            'date_of_birth' => $this->faker->dateTimeBetween('-16 years', '-7 years')->format('Y-m-d'),
            'gender' => $gender,
            'parent_name' => $hasParent ? $this->parentName($ethnicity) : null,
            'parent_email' => $hasParent ? $this->faker->safeEmail() : null,
            'parent_phone' => $hasParent ? $this->faker->numerify('01#-###-####') : null,
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
            'is_active' => true,
        ];
    }

    /**
     * A plausible Malaysian parent name in the same tradition.
     */
    private function parentName(string $ethnicity): string
    {
        return match ($ethnicity) {
            'malay' => $this->faker->randomElement(['Encik', 'Puan']).' '.$this->faker->randomElement($this->malayFather),
            'chinese' => $this->faker->randomElement(['Mr', 'Mdm']).' '.$this->faker->randomElement($this->chineseSurname),
            'indian' => $this->faker->randomElement(['Mr', 'Mrs']).' '.$this->faker->randomElement($this->indianFather),
            default => $this->faker->name(),
        };
    }
}
