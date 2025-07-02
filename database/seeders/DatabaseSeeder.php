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

        // Crear categorías con amount por defecto
        $categories = collect([
            ['name' => 'Renta', 'amount' => 2500],
            ['name' => 'Servicios', 'amount' => 4500],
            ['name' => 'Mercado', 'amount' => 12000],
            ['name' => 'Hogar', 'amount' => 500],
            ['name' => 'Educación', 'amount' => 1000],
            ['name' => 'Bebidas', 'amount' => 750],
            ['name' => 'Comida rápida', 'amount' => 750],
            ['name' => 'Salud', 'amount' => 1000],
            ['name' => 'Transporte', 'amount' => 500],
            ['name' => 'Ropa', 'amount' => 500],
            ['name' => 'Belleza', 'amount' => 500],
            ['name' => 'Crédito', 'amount' => 3000],
        ])->map(fn ($data) => Category::create($data));

        // Crear las categorías
        $categories = Category::all();

        // Por cada categoría, crea budgets
        // foreach ($categories as $category) {
        //     Budget::factory(2)->create([
        //         'category_id' => $category->id,
        //     ]);
        // }

        // Crear un Revenue para junio 2025

        $revenue = Revenue::create([
            'date' => '2025-06-01',
            'amount' => 37000,
            'extra' => 5000,
            'saving' => 8000,
        ]);

        // Crear algunos gastos para ese revenue

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Renta')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Casa',
            'cost' => 2500,
            'type' => 'Gasto',
            'date' => '2025-06-02',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Mercado')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Supermercado',
            'cost' => 1300,
            'type' => 'Gasto',
            'date' => '2025-06-05',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Salud')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Consulta médica',
            'cost' => 60,
            'type' => 'Gasto',
            'date' => '2025-06-10',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Salud')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Medicina',
            'cost' => 400,
            'type' => 'Extra',
            'date' => '2025-06-10',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Comida rápida')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Dominos',
            'cost' => 300,
            'type' => 'Extra',
            'date' => '2025-06-15',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Hogar')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Teclado',
            'cost' => 3000,
            'type' => 'Ahorro',
            'date' => '2025-06-21',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Servicios')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Luz',
            'cost' => 600,
            'type' => 'Gasto',
            'date' => '2025-06-1',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Servicios')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Agua',
            'cost' => 300,
            'type' => 'Gasto',
            'date' => '2025-06-2',
        ]);

        // Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'a@f.mx',
            'password' => bcrypt('a@f.mx'), // o Hash::make()
        ]);

        // User::factory(5)->create();
    }
}
