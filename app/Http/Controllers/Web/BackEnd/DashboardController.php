<?php

namespace App\Http\Controllers\Web\BackEnd;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize(PermissionEnum::READ_DASHBOARD->value);

        // Ambil date range dari request, default: 30 hari terakhir
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : now()->endOfDay();

        // Widget data
        $totalTransactions = Payment::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalSalesAmount = Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $totalQtySold = SaleItem::whereBetween('created_at', [$startDate, $endDate])->sum('qty');

        // Chart data - sales per month (in Rupiah)
        $monthlySales = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Chart data - quantity per item
        $itemQtyChart = SaleItem::with('item')
            ->selectRaw('item_id, SUM(qty) as total_qty')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('item_id')
            ->get();

        return view('pages.dashboard.index', [
            'title' => 'Dashboard',
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
            'totalTransactions' => $totalTransactions,
            'totalSalesAmount' => $totalSalesAmount,
            'totalQtySold' => $totalQtySold,
            'monthlySales' => $monthlySales,
            'itemQtyChart' => $itemQtyChart,
        ]);
    }
}
