<?php

namespace Database\Factories;

use App\Models\Revenue;
use Illuminate\Database\Eloquent\Factories\Factory;

class RevenueFactory extends Factory
{
    protected $model = Revenue::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(5000, 15000),
            'date' => $this->faker->date(),
            'extra' => $this->faker->boolean() ? $this->faker->numberBetween(100, 2000) : null,
            'saving' => $this->faker->boolean() ? $this->faker->numberBetween(100, 2000) : null,
        ];
    }
}
