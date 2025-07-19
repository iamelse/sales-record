@extends('layouts.app')

@section('content')
    <main>
        <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
            <!-- Header Section -->
            <div class="flex px-6 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Item</h1>
                    <p class="text-gray-600 dark:text-gray-400">Update the item details.</p>
                </div>
            </div>

            <!-- Form Section -->
            <div class="border-gray-100 p-5 dark:border-gray-800 sm:p-6">
                <div class="rounded-2xl px-6 pb-8 pt-4 border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                    <form action="{{ route('be.item.update', $item->code) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mt-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Name <span class="text-error-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $item->name) }}"
                                placeholder="Enter item name"
                                class="h-11 w-full text-sm mt-1 px-4 py-2.5 border @error('name') border-red-500 @else border-gray-300 @enderror dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300"
                                required>
                            @error('name')
                                <span class="text-xs mt-1 font-medium text-red-500">* {{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="mt-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Price <span class="text-error-500">*</span>
                            </label>
                            <input
                                type="number"
                                name="price"
                                value="{{ old('price', $item->price) }}"
                                placeholder="Enter price (e.g. 15000)"
                                step="0.01"
                                min="0"
                                class="h-11 w-full text-sm mt-1 px-4 py-2.5 border @error('price') border-red-500 @else border-gray-300 @enderror dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300"
                                required>
                            @error('price')
                                <span class="text-xs mt-1 font-medium text-red-500">* {{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Image -->
                        <div class="mt-4">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Image
                            </label>
                            <input
                                type="file"
                                name="image"
                                accept="image/*"
                                class="h-11 w-full text-sm mt-1 px-3 py-2.5 border @error('image') border-red-500 @else border-gray-300 @enderror dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300">
                            @error('image')
                                <span class="text-xs mt-1 font-medium text-red-500">* {{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                    class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-blue-500 bg-blue-600 text-white font-medium transition-all hover:bg-blue-700 hover:border-blue-600 focus:ring focus:ring-blue-300 dark:bg-blue-700 dark:border-blue-600 dark:hover:bg-blue-800">
                                Update Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('bottom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 shadow-lg',
                        title: 'font-normal text-base text-gray-800 dark:text-gray-200'
                    }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "error",
                    title: "{{ session('error') }}",
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 shadow-lg',
                        title: 'font-normal text-base text-gray-800 dark:text-gray-200'
                    }
                });
            @endif
        });
    </script>
@endsection
