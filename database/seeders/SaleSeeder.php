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

        if (!$user || $items->isEmpty()) {
            $this->command->error('User or Items not found. Please seed them first.');
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            // Hitung jumlah sale saat ini dan tambahkan offset $i untuk buat kode unik
            $saleCount = Sale::count() + $i;
            $code = 'INV-' . now()->format('Ymd') . '-' . str_pad($saleCount, 3, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'customer_name' => fake()->name(),
                'code' => $code,
                'user_id' => $user->id,
                'status' => 'Belum Dibayar',
                'total_price' => 0, // akan dihitung di bawah
            ]);

            $total = 0;

            // Ambil 2–4 item secara acak dari item yang tersedia
            $randomItems = $items->random(rand(2, 4));

            foreach ($randomItems as $item) {
                $qty = rand(1, 5);
                $price = $item->price;
                $totalPrice = $qty * $price;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item->id,
                    'qty' => $qty,
                    'price' => $price,
                    'total_price' => $totalPrice,
                ]);

                $total += $totalPrice;
            }

            // Update total_price pada Sale
            $sale->update(['total_price' => $total]);
        }

        $this->command->info('5 penjualan berhasil dibuat.');
    }
}
