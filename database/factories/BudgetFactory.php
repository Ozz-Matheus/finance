<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(500, 10000),
            'date' => $this->faker->date(),
            'category_id' => Category::factory(),
        ];
    }
}
