<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Item::create([
                'code' => generateUniqueItemCode(3, 6),
                'name' => 'Item ' . $i,
                'price' => rand(10000, 50000),
                'image' => null,
            ]);
        }
    }
}
