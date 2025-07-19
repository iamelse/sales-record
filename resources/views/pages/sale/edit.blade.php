@extends('layouts.app')

@section('content')
    <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6"
             x-data="saleForm({!! htmlspecialchars(json_encode($sale->items), ENT_QUOTES, 'UTF-8') !!}, '{{ $sale->code }}', '{{ $sale->customer_name }}')">

        <div class="flex px-6 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Sale</h1>
                    <p class="text-gray-600 dark:text-gray-400">Edit existing sale data and items.</p>
                </div>
            </div>

            <div class="border-gray-100 p-5 dark:border-gray-800 sm:p-6">
                <div class="rounded-2xl px-6 pb-8 pt-4 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <form method="POST" action="{{ route('be.sale.update', $sale->code) }}">
                        @csrf
                        @method('PUT')

                        <!-- Sale Code -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Sale Code</label>
                            <input type="text" name="code" x-model="code" readonly
                                   class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                        </div>

                        <!-- Customer Name -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Customer Name</label>
                            <input type="text" name="customer_name" x-model="customer_name"
                                   class="w-full mt-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300"
                                   required>
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
                                    <th class="px-4 py-2">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template x-for="(row, index) in items" :key="index">
                                    <tr>
                                        <td class="py-2">
                                            <select :name="'items['+index+'][item_id]'"
                                                    x-model="row.item_id"
                                                    x-init="$nextTick(() => new TomSelect($el, { create: false, allowEmptyOption: true }))"
                                                    @change="updatePrice(index)"
                                                    class="tom-select w-full">
                                                <option value="">-- Select Item --</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}">
                                                        {{ $item->name }} - Rp{{ number_format($item->price, 0, ',', '.') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" min="1" :name="'items['+index+'][qty]'" x-model="row.qty"
                                                   class="w-full px-2 py-2 border border-gray-300 rounded-md dark:bg-gray-900 dark:border-gray-700">
                                        </td>
                                        <td class="px-4 py-2">
                                            <!-- Nilai tampil (Rupiah) -->
                                            <input type="text" readonly :value="formatRupiah(row.price)"
                                                   class="w-full px-2 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md">

                                            <!-- Hidden input untuk nilai asli -->
                                            <input type="hidden" :name="'items['+index+'][price]'" :value="row.price">
                                        </td>
                                        <td class="px-4 py-2">
                                            <!-- Nilai tampil (Rupiah) -->
                                            <input type="text" readonly :value="formatRupiah(row.qty * row.price)"
                                                   class="w-full px-2 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md">

                                            <!-- Hidden input untuk nilai asli -->
                                            <input type="hidden" :name="'items['+index+'][total_price]'" :value="row.qty * row.price">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-600 hover:underline text-xs">Remove</button>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>

                            <button type="button" @click="addItem()" class="mt-4 text-blue-600 hover:underline text-sm">+ Add Item</button>
                        </div>

                        <!-- Grand Total -->
                        <div class="mt-6 text-right text-lg font-semibold text-gray-800 dark:text-white">
                            <p class="text-xl font-semibold">Total: <span x-text="grandTotal()"></span></p>
                            <!-- Hidden input for raw value if needed -->
                            <input type="hidden" name="grand_total" :value="items.reduce((sum, item) => sum + (item.qty * item.price), 0)">
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Update Sale
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('bottom-scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
        function saleForm(existingItems = [], code = '', customerName = '') {
            return {
                itemPrices: {
                    @foreach($items as $item)
                        {{ $item->id }}: {{ $item->price }},
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
                    : [{ item_id: '', qty: 1, price: 0 }],

                addItem() {
                    this.items.push({ item_id: '', qty: 1, price: 0 });
                },

                removeItem(index) {
                    if (this.items.length > 1) this.items.splice(index, 1);
                },

                updatePrice(index) {
                    const itemId = this.items[index].item_id;
                    if (itemId && this.itemPrices[itemId]) {
                        this.items[index].price = this.itemPrices[itemId];
                    } else {
                        this.items[index].price = 0;
                    }
                },

                grandTotal() {
                    const total = this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
                    return this.formatRupiah(total);
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
