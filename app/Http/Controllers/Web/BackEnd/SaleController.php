<?php

namespace App\Http\Controllers\Web\BackEnd;

use App\Enums\PermissionEnum;
use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Sale\StoreSaleRequest;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        Gate::authorize(PermissionEnum::READ_SALE->value);

        $allowedFilterFields = ['code'];
        $allowedSortFields = ['code', 'created_at', 'updated_at'];
        $limits = [10, 25, 50, 100];

        $sales = Sale::search(
            keyword: $request->keyword,
            columns: $allowedFilterFields,
        )
            ->when($request->filled(['start_date', 'end_date']), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            })
            ->sort(
                sort_by: $request->sort_by ?? 'created_at',
                sort_order: $request->sort_order ?? 'DESC'
            )
            ->paginate($request->query('limit') ?? 10);

        return view('pages.sale.index', [
            'title' => 'Sales',
            'sales' => $sales,
            'allowedSortFields' => $allowedSortFields,
            'allowedFilterFields' => $allowedFilterFields,
            'limits' => $limits,
        ]);
    }

    public function create(): View
    {
        Gate::authorize(PermissionEnum::CREATE_SALE->value);

        $latest = Sale::latest()->first();
        $code = 'INV-' . now()->format('Ymd') . '-' . str_pad(optional($latest)->id + 1, 3, '0', STR_PAD_LEFT);

        return view('pages.sale.create', [
            'title' => 'New Sale',
            'items' => Item::all(),
            'generatedCode' => $code,
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        Gate::authorize(PermissionEnum::CREATE_SALE->value);

        try {
            // Buat kode invoice unik (contoh: INV-20250718-001)
            $countToday = Sale::whereDate('created_at', Carbon::today())->count() + 1;
            $code = 'INV-' . now()->format('Ymd') . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Hitung total harga dari items
            $totalPrice = collect($request->items)->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });

            // Simpan data sale utama
            $sale = Sale::create([
                'customer_name' => $request->customer_name,
                'code' => $code,
                'user_id' => auth()->id(),
                'status' => 'Belum Dibayar',
                'total_price' => $totalPrice,
            ]);

            // Simpan masing-masing sale item
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total_price' => $item['qty'] * $item['price'],
                ]);
            }

            return redirect()->route('be.sale.index')
                ->with('success', 'Penjualan berhasil disimpan.');

        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('be.sale.create')
                ->with('error', $exception->getMessage());
        }
    }

    public function show(Sale $sale): View
    {
        Gate::authorize(PermissionEnum::READ_SALE->value);

        $sale = $sale->load('items');

        return view('pages.sale.show', [
            'title' => 'Detail Sale',
            'sale' => $sale,
            'items' => Item::all(),
        ]);
    }

    public function edit(Sale $sale): View
    {
        Gate::authorize(PermissionEnum::UPDATE_SALE->value);

        if ($sale->status !== SaleStatus::UNPAID->value) {
            abort(404,);
        }

        $sale = $sale->load('items');

        return view('pages.sale.edit', [
            'title' => 'Edit Sale',
            'sale' => $sale,
            'items' => Item::all(),
        ]);
    }

    public function update(Request $request, Sale $sale): RedirectResponse
    {
        Gate::authorize(PermissionEnum::UPDATE_SALE->value);

        if ($sale->status !== SaleStatus::UNPAID->value) {
            abort(404,);
        }

        try {
            // Hitung total harga dari item baru
            $totalPrice = collect($request->items)->sum(function ($item) {
                return $item['qty'] * $item['price'];
            });

            // Update data utama sale
            $sale->update([
                'customer_name' => $request->customer_name,
                'total_price' => $totalPrice,
            ]);

            // Hapus item sebelumnya
            $sale->items()->delete();

            // Tambahkan item baru
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item['item_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total_price' => $item['qty'] * $item['price'],
                ]);
            }

            return redirect()->route('be.sale.index')
                ->with('success', 'Penjualan berhasil diperbarui.');

        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('be.sale.edit', $sale->id)
                ->with('error', $exception->getMessage());
        }
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_SALE->value);

            if ($sale->status === SaleStatus::PAID->value) {
                return redirect()
                    ->route('be.sale.index')
                    ->with('error', 'Sale that has been fully paid cannot be deleted.');
            }

            // Hapus semua item yang terkait dengan sale ini
            $sale->items()->delete();

            // Hapus sale
            $sale->delete();

            return redirect()
                ->route('be.sale.index')
                ->with('success', 'Sale deleted successfully.');
        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            Log::error("Error deleting sale (Code: {$sale->code}): " . $e->getMessage());

            return redirect()
                ->route('be.sale.index')
                ->with('error', 'An error occurred while deleting the sale.');
        }
    }

    public function massDestroy(Request $request): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_SALE->value);

            $saleCodesArray = explode(',', $request->input('codes', ''));

            if (!empty($saleCodesArray)) {
                $sales = Sale::whereIn('code', $saleCodesArray)->get();

                foreach ($sales as $sale) {
                    if ($sale->status === SaleStatus::PAID->value) {
                        continue;
                    }

                    $sale->items()->delete();
                    $sale->delete();
                }
            }

            return redirect()
                ->route('be.sale.index')
                ->with('success', 'Sales deleted successfully. Paid sales were skipped.');
        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            Log::error('Error deleting sales: ' . $e->getMessage());

            return redirect()
                ->route('be.sale.index')
                ->with('error', 'An error occurred while deleting the sales.');
        }
    }
}
