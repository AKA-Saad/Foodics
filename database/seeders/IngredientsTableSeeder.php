<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IngredientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingredients = [
            [
                'name' => 'Beef',
                'stock' => 0,
                'total_stock' => 20,
                'unit' => 'kg',
            ],
            [
                'name' => 'Cheese',
                'stock' => 0,
                'total_stock' => 5,
                'unit' => 'kg',
            ],
            [
                'name' => 'Onion',
                'stock' => 0,
                'total_stock' => 1,
                'unit' => 'kg',
            ],
        ];


        foreach ($ingredients as $ingredient) {
            Ingredient::create($ingredient);
        }
    }
}
