@extends('layouts.app')

@section('content')
    <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Create Payment</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Below is a summary of items in this sale. Payment will be marked as <b>Sudah Dibayar</b>.
                    </p>
                </div>
            </div>

            <!-- Card -->
            <div class="mt-6">
                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
                    <form method="POST" action="{{ route('be.payment.store', $sale) }}">
                        @csrf

                        <!-- Payment Code -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Payment Code
                            </label>
                            <input type="text" name="code" value="{{ $generatedCode }}" readonly
                                   class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        </div>

                        <!-- Customer Name -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Customer Name
                            </label>
                            <input type="text" name="customer_name" value="{{ $sale->customer_name }}" readonly
                                   class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        </div>

                        <!-- Items Table -->
                        <div class="mt-6 overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-700 dark:text-gray-300">
                                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                <tr>
                                    <th class="px-4 py-2">Item</th>
                                    <th class="px-4 py-2">Qty</th>
                                    <th class="px-4 py-2">Price</th>
                                    <th class="px-4 py-2">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($saleItems as $index => $saleItem)
                                    <tr>
                                        <td class="px-4 py-2">
                                            {{ $saleItem->item->name }}
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $saleItem->item_id }}">
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $saleItem->qty }}
                                            <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $saleItem->qty }}">
                                        </td>
                                        <td class="px-4 py-2">
                                            Rp{{ number_format($saleItem->price, 0, ',', '.') }}
                                            <input type="hidden" name="items[{{ $index }}][price]" value="{{ $saleItem->price }}">
                                        </td>
                                        <td class="px-4 py-2">
                                            Rp{{ number_format($saleItem->total_price, 0, ',', '.') }}
                                            <input type="hidden" name="items[{{ $index }}][total_price]" value="{{ $saleItem->total_price }}">
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total -->
                        <div class="mt-6 text-right text-lg font-semibold text-gray-800 dark:text-white">
                            <p class="text-xl font-semibold">
                                Total: Rp{{ number_format($saleItems->sum('total_price'), 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Pay Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
