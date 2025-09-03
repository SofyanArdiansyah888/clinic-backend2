<?php

namespace Database\Factories;

use App\Models\MonthlySequence;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlySequence>
 */
class MonthlySequenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MonthlySequence::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model' => $this->faker->randomElement(['PAS', 'ANT', 'TRT', 'BRG', 'APT']),
            'year_month' => $this->faker->numberBetween(20, 25) . str_pad($this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT),
            'counter' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
