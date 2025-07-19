@extends('layouts.app')

@section('content')
    <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6"
             x-data="saleForm({!! htmlspecialchars(json_encode($sale->items), ENT_QUOTES, 'UTF-8') !!}, '{{ $sale->code }}', '{{ $sale->customer_name }}')">

            <div class="flex px-6 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Sale Detail</h1>
                    <p class="text-gray-600 dark:text-gray-400">View detail of sale and its items.</p>
                </div>
            </div>

            <div class="border-gray-100 p-5 dark:border-gray-800 sm:p-6">
                <div class="rounded-2xl px-6 pb-8 pt-4 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                    <!-- Sale Code -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sale Code</label>
                        <input type="text" x-model="code" readonly
                               class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    </div>

                    <!-- Customer Name -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Customer Name</label>
                        <input type="text" x-model="customer_name" readonly
                               class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    </div>

                    <!-- Items Table -->
                    <div class="mt-6">
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
                            <template x-for="(row, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2">
                                        <span x-text="getItemName(row.item_id)"></span>
                                    </td>
                                    <td class="px-4 py-2" x-text="row.qty"></td>
                                    <td class="px-4 py-2" x-text="formatRupiah(row.price)"></td>
                                    <td class="px-4 py-2" x-text="formatRupiah(row.qty * row.price)"></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Grand Total -->
                    <div class="mt-6 text-right text-lg font-semibold text-gray-800 dark:text-white">
                        <p class="text-xl font-semibold">Total: <span x-text="grandTotal()"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('bottom-scripts')
    <script>
        function saleForm(existingItems = [], code = '', customerName = '') {
            return {
                itemPrices: {
                    @foreach($items as $item)
                        {{ $item->id }}: {{ $item->price }},
                    @endforeach
                },
                itemNames: {
                    @foreach($items as $item)
                        {{ $item->id }}: '{{ $item->name }}',
                    @endforeach
                },

                code: code,
                customer_name: customerName,

                items: existingItems.length > 0
                    ? existingItems.map(item => ({
                        item_id: item.item_id,
                        qty: item.qty,
                        price: item.price,
                    }))
                    : [],

                grandTotal() {
                    const total = this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
                    return this.formatRupiah(total);
                },

                getItemName(id) {
                    return this.itemNames[id] ?? 'Unknown Item';
                },

                formatRupiah(angka) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(angka);
                }
            }
        }
    </script>
@endsection
