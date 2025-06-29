<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'cost' => $this->faker->numberBetween(100, 5000),
            'date' => $this->faker->date(),
            'type' => $this->faker->randomElement(['Sueldo', 'Extra']),
            'category_id' => Category::factory(),
        ];
    }
}
