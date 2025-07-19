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
            $paymentCount = Payment::count() + 1; // Use Payment count instead of Sale
            $paymentCode = 'PYM-' . now()->format('Ymd') . '-' . str_pad($paymentCount, 3, '0', STR_PAD_LEFT);

            Payment::create([
                'code' => $paymentCode,
                'sale_id' => $sale->id,
                'amount' => $sale->total_price,
            ]);

            // Update sale status
            $sale->update([
                'status' => 'Sudah Dibayar',
            ]);
        }
    }
}
