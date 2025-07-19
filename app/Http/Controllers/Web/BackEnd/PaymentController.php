<?php

namespace App\Http\Controllers\Web\BackEnd;

use App\Enums\PermissionEnum;
use App\Enums\SaleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Sale\StoreSaleRequest;
use App\Models\Item;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View | RedirectResponse
    {
        Gate::authorize(PermissionEnum::READ_PAYMENT->value);

        $allowedFilterFields = ['code'];
        $allowedSortFields = ['code', 'created_at', 'updated_at'];
        $limits = [10, 25, 50, 100];

        $payments = Payment::with('sale')->search(
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

        return view('pages.payment.index', [
            'title' => 'Payments',
            'payments' => $payments,
            'allowedSortFields' => $allowedSortFields,
            'allowedFilterFields' => $allowedFilterFields,
            'limits' => $limits,
        ]);
    }

    public function create(Sale $sale): View
    {
        Gate::authorize(PermissionEnum::CREATE_PAYMENT->value);

        $paymentCount = Payment::whereDate('created_at', now())->count() + 1;
        $code = 'PYM-' . now()->format('Ymd') . '-' . str_pad($paymentCount, 3, '0', STR_PAD_LEFT);

        return view('pages.payment.create', [
            'title' => 'New Payment',
            'generatedCode' => $code,
            'saleItems' => $sale->load('items')->items,
            'sale' => $sale,
        ]);
    }

    public function store(StoreSaleRequest $request, Sale $sale): RedirectResponse
    {
        Gate::authorize(PermissionEnum::CREATE_PAYMENT->value);

        DB::beginTransaction();

        try {
            // Generate unique payment code
            $countToday = Payment::whereDate('created_at', now())->count() + 1;
            $code = 'PYM-' . now()->format('Ymd') . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Calculate total amount from sale items
            $totalPrice = $sale->items->sum('total_price');

            // Save payment record
            Payment::create([
                'code'     => $code,
                'sale_id'  => $sale->id,
                'amount'   => $totalPrice,
            ]);

            // Update sale status to PAID
            $sale->update([
                'status' => SaleStatus::PAID, // or just 'PAID' if not using enum
            ]);

            DB::commit();

            return redirect()->route('be.sale.index')
                ->with('success', 'Payment has been successfully recorded and the sale is now marked as PAID.');
        } catch (AuthorizationException $authEx) {
            DB::rollBack();
            Log::error($authEx->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());

            return redirect()->route('be.payment.create', $sale)
                ->with('error', 'An error occurred while saving the payment: ' . $ex->getMessage());
        }
    }

    public function show(Payment $payment): View
    {
        Gate::authorize(PermissionEnum::READ_PAYMENT->value);

        $sale = $payment->sale;

        $saleItems = $sale->items()->with('item')->get();

        return view('pages.payment.show', [
            'payment' => $payment,
            'sale' => $sale,
            'saleItems' => $saleItems,
        ]);
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_PAYMENT->value);

            DB::beginTransaction();

            // Ubah status sale menjadi UNPAID
            $payment->sale->update([
                'status' => SaleStatus::UNPAID,
            ]);

            // Hapus pembayaran
            $payment->delete();

            DB::commit();

            return redirect()
                ->route('be.payment.index')
                ->with('success', 'Payment deleted successfully. The associated sale is now marked as UNPAID.');
        } catch (AuthorizationException $authorizationException) {
            DB::rollBack();
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment: ' . $e->getMessage());

            return redirect()
                ->route('be.payment.index')
                ->with('error', 'An error occurred while deleting the payment.');
        }
    }

    public function massDestroy(Request $request): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_PAYMENT->value);

            $paymentCodes = explode(',', $request->input('codes', ''));

            if (empty($paymentCodes)) {
                return redirect()
                    ->route('be.payment.index')
                    ->with('error', 'No payment codes were provided.');
            }

            DB::beginTransaction();

            $payments = Payment::whereIn('code', $paymentCodes)->get();

            foreach ($payments as $payment) {
                // Update related sale status to UNPAID
                $payment->sale->update([
                    'status' => SaleStatus::UNPAID,
                ]);

                // Delete the payment
                $payment->delete();
            }

            DB::commit();

            return redirect()
                ->route('be.payment.index')
                ->with('success', 'Payments deleted successfully. Associated sales have been marked as UNPAID.');
        } catch (AuthorizationException $authorizationException) {
            DB::rollBack();
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payments: ' . $e->getMessage());

            return redirect()
                ->route('be.payment.index')
                ->with('error', 'An error occurred while deleting the payments.');
        }
    }
}
