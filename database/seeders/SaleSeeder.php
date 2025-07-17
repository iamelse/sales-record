<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $items = Item::all();

        for ($i = 1; $i <= 5; $i++) {
            $sale = Sale::create([
                'code' => 'PNJ-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'status' => 'Belum Dibayar',
                'total_price' => 0, // akan diupdate
            ]);

            $total = 0;

            // Masukkan 2-4 item random
            $saleItems = $items->random(rand(2, 4));
            foreach ($saleItems as $item) {
                $qty = rand(1, 5);
                $price = $item->price;
                $total_price = $qty * $price;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'price' => $price,
                    'total_price' => $total_price,
                ]);

                $total += $total_price;
            }

            // Update total price sale
            $sale->update(['total_price' => $total]);
        }
    }
}
