<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sales = Sale::all();

        foreach ($sales->take(3) as $index => $sale) {
            Payment::create([
                'code' => 'PYM-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'sale_id' => $sale->id,
                'amount' => $sale->total_price,
            ]);

            // Update status di sale
            $sale->update([
                'status' => 'Sudah Dibayar',
            ]);
        }
    }
}
