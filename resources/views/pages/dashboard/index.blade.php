@extends('layouts.app')

@section('content')
    <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ getGreeting() }}, {{ getFirstName(Auth::user()->name) }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">Welcome to your dashboard!</p>
                </div>
            </div>

            <!-- Filter -->
            <form method="GET" class="mt-6 mb-4">
                <div class="flex flex-col sm:flex-row gap-4 items-end">
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-300">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}"
                               class="block w-full mt-1 px-4 py-2 border rounded-md dark:bg-gray-800 dark:text-gray-200" />
                    </div>
                    <div>
                        <label class="text-sm text-gray-700 dark:text-gray-300">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}"
                               class="block w-full mt-1 px-4 py-2 border rounded-md dark:bg-gray-800 dark:text-gray-200" />
                    </div>
                    <div>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Widgets -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                <div class="p-5 bg-white border rounded-xl dark:bg-white/[0.03] dark:border-gray-800">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalTransactions }}</p>
                </div>
                <div class="p-5 bg-white border rounded-xl dark:bg-white/[0.03] dark:border-gray-800">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Sales (Rp)</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">Rp{{ number_format($totalSalesAmount, 0, ',', '.') }}</p>
                </div>
                <div class="p-5 bg-white border rounded-xl dark:bg-white/[0.03] dark:border-gray-800">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Quantity Sold</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalQtySold }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-5 border rounded-xl dark:bg-white/[0.03] dark:border-gray-800">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Sales Amount per Month</h2>
                    <canvas id="salesPerMonthChart" height="150"></canvas>
                </div>

                <div class="bg-white p-5 border rounded-xl dark:bg-white/[0.03] dark:border-gray-800">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Items Sold per Item</h2>
                    <canvas id="itemQtyChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </main>
@endsection

<!-- Chart Scripts -->
@section('bottom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const salesPerMonthCtx = document.getElementById('salesPerMonthChart').getContext('2d');
        new Chart(salesPerMonthCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlySales->pluck('month')) !!},
                datasets: [{
                    label: 'Sales (Rp)',
                    data: {!! json_encode($monthlySales->pluck('total')) !!},
                    backgroundColor: '#2563eb'
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        const itemQtyCtx = document.getElementById('itemQtyChart').getContext('2d');
        new Chart(itemQtyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($itemQtyChart->pluck('item.name')) !!},
                datasets: [{
                    label: 'Qty Sold',
                    data: {!! json_encode($itemQtyChart->pluck('total_qty')) !!},
                    backgroundColor: '#10b981'
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
