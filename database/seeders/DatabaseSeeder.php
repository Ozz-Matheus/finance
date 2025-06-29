<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Revenue;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 5 categorías
        $categories = Category::factory(5)->create();

        // Por cada categoría, crea budgets y bills
        foreach ($categories as $category) {
            Budget::factory(2)->create([
                'category_id' => $category->id,
            ]);

            Bill::factory(3)->create([
                'category_id' => $category->id,
            ]);
        }

        // Crear ingresos
        Revenue::factory(10)->create();

        // Prueba
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'a@f.mx',
            'password' => bcrypt('a@f.mx'), // o Hash::make()
        ]);

        User::factory(5)->create();
    }
}
