<?php

namespace App\Http\Controllers\Web\BackEnd;

use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Item\StoreItemRequest;
use App\Http\Requests\Web\Item\UpdateItemRequest;
use App\Models\Item;
use App\Services\ImageManagementService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected ImageManagementService $imageManagementService
    ) {}

    public function index(Request $request): View | RedirectResponse
    {
        Gate::authorize(PermissionEnum::READ_ITEM->value);

        $allowedFilterFields = ['code', 'name', 'price'];
        $allowedSortFields = ['code', 'name', 'price', 'created_at', 'updated_at'];
        $limits = [10, 25, 50, 100];

        $items = Item::search(
            keyword: $request->keyword,
            columns: $allowedFilterFields,
        )->sort(
            sort_by: $request->sort_by ?? 'name',
            sort_order: $request->sort_order ?? 'ASC'
        )->paginate($request->query('limit') ?? 10);

        return view('pages.item.index', [
            'title' => 'Item',
            'items' => $items,
            'allowedFilterFields' => $allowedFilterFields,
            'allowedSortFields' => $allowedSortFields,
            'limits' => $limits
        ]);
    }

    public function create(): View
    {
        Gate::authorize(PermissionEnum::CREATE_ITEM->value);

        return view('pages.item.create', [
            'title' => 'New Item',
        ]);
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        Gate::authorize(PermissionEnum::CREATE_ITEM->value);

        try {
            $imagePath = $this->_handleImageUpload($request, null);

            $code = $request->filled('code') ? $request->code : generateUniqueItemCode(3, 6);

            Item::create([
                'name' => $request->name,
                'code' => $code,
                'price' => $request->price,
                'image' => $imagePath,
            ]);

            return redirect()->route('be.item.index')
                ->with('success', 'Item created successfully.');
        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('be.item.create')
                ->with('error', $exception->getMessage());
        }
    }

    public function edit(Item $item): View
    {
        Gate::authorize(PermissionEnum::UPDATE_ITEM->value);

        return view('pages.item.edit', [
            'item' => $item,
            'title' => 'Edit Item',
        ]);
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        Gate::authorize(PermissionEnum::UPDATE_ITEM->value);

        try {
            $imagePath = $this->_handleImageUpload($request, $item);

            $item->update([
                'name' => $request->name,
                'price' => $request->price,
                'image' => $imagePath,
                'code' => $request->filled('code') ? $request->code : $item->code,
            ]);

            return redirect()->route('be.item.index')
                ->with('success', 'Item updated successfully.');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return redirect()->route('be.item.edit', $item->code)
                ->with('error', $exception->getMessage());
        }
    }

    public function destroy(Item $item): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_ITEM->value);

            $this->imageManagementService->destroyImage($item->image);

            $item->delete();

            return redirect()
                ->route('be.item.index')
                ->with('success', 'Item deleted successfully.');
        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            Log::error("Error deleting item (Code: {$item->code}): " . $e->getMessage());

            return redirect()
                ->route('be.item.index')
                ->with('error', 'An error occurred while deleting the item.');
        }
    }

    public function massDestroy(Request $request): RedirectResponse
    {
        try {
            Gate::authorize(PermissionEnum::DELETE_ITEM->value);

            $itemCodesArray = explode(',', $request->input('codes', ''));

            if (!empty($itemCodesArray)) {
                $items = Item::whereIn('code', $itemCodesArray)->get();

                foreach ($items as $item) {
                    $this->imageManagementService->destroyImage($item->image);
                    $item->delete();
                }
            }

            return redirect()
                ->route('be.item.index')
                ->with('success', 'Items deleted successfully.');
        } catch (AuthorizationException $authorizationException) {
            Log::error($authorizationException->getMessage());

            abort(403, 'This action is unauthorized.');
        } catch (\Exception $e) {
            Log::error('Error deleting items: ' . $e->getMessage());

            return redirect()
                ->route('be.item.index')
                ->with('error', 'An error occurred while deleting the items.');
        }
    }

    private function _handleImageUpload(Request $request, $item): ?string
    {
        $currentImagePath = $item?->image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');

            return $this->imageManagementService->uploadImage($image, [
                'currentImagePath' => $currentImagePath,
                'disk' => env('FILESYSTEM_DISK'),
                'folder' => 'uploads/items'
            ]);
        }

        return $currentImagePath;
    }
}
