<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Revenue;
use Illuminate\Database\Seeder;

class BaseAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Crear categorías con amount por defecto */
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

        /* Crear un Revenue para Julio 2025 */

        $revenue = Revenue::create([
            'date' => '2025-07-01',
            'amount' => 37000,
            'extra' => 5000,
            'saving' => 8000,
        ]);

        /* Metodos de pago */
        $method = collect([
            ['name' => 'Efectivo'],
            ['name' => 'Débito'],
            ['name' => 'Crédito'],
        ])->map(fn ($data) => PaymentMethod::create($data));

        /* Crear algunos gastos para ese revenue */

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Renta')->id,
            'payment_method_id' => $method->firstWhere('name', 'Débito')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Casa',
            'cost' => 2500,
            'type' => 'Gasto',
            'date' => '2025-07-02',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Mercado')->id,
            'revenue_id' => $revenue->id,
            'payment_method_id' => $method->firstWhere('name', 'Débito')->id,
            'name' => 'Supermercado',
            'cost' => 1300,
            'type' => 'Gasto',
            'date' => '2025-07-05',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Salud')->id,
            'payment_method_id' => $method->firstWhere('name', 'Efectivo')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Consulta médica',
            'cost' => 60,
            'type' => 'Gasto',
            'date' => '2025-07-10',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Salud')->id,
            'payment_method_id' => $method->firstWhere('name', 'Efectivo')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Medicina',
            'cost' => 400,
            'type' => 'Extra',
            'date' => '2025-07-10',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Comida rápida')->id,
            'payment_method_id' => $method->firstWhere('name', 'Crédito')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Dominos',
            'cost' => 300,
            'type' => 'Extra',
            'date' => '2025-07-15',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Hogar')->id,
            'payment_method_id' => $method->firstWhere('name', 'Débito')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Teclado',
            'cost' => 3000,
            'type' => 'Ahorro',
            'date' => '2025-07-21',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Servicios')->id,
            'payment_method_id' => $method->firstWhere('name', 'Efectivo')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Luz',
            'cost' => 600,
            'type' => 'Gasto',
            'date' => '2025-07-1',
        ]);

        Bill::create([
            'category_id' => $categories->firstWhere('name', 'Servicios')->id,
            'payment_method_id' => $method->firstWhere('name', 'Efectivo')->id,
            'revenue_id' => $revenue->id,
            'name' => 'Agua',
            'cost' => 300,
            'type' => 'Gasto',
            'date' => '2025-07-2',
        ]);
    }
}
