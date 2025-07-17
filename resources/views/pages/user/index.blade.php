@php
    use App\Enums\PermissionEnum;
@endphp

@extends('layouts.app')

@section('content')
<!-- ===== Main Content Start ===== -->
<main>
   <div class="p-4 mx-auto max-w-screen-2xl md:p-6">

    <!-- Header Section -->
    <div class="flex px-6 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">User Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage user data</p>
        </div>
        @can(PermissionEnum::CREATE_USER, $users)
        <a href="{{ route('be.user.create') }}" 
            class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-blue-500 bg-blue-600 text-white font-medium transition-all hover:bg-blue-700 hover:border-blue-600 focus:ring focus:ring-blue-300 dark:bg-blue-700 dark:border-blue-600 dark:hover:bg-blue-800">
            <i class="bx bx-plus text-lg"></i>
            New User
        </a>
        @endcan
    </div>
    
    <!-- Table Section -->
    <div class="border-gray-100 p-5 dark:border-gray-800 sm:p-6" x-data="{ selected: [] }">
        <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex flex-col gap-2 px-5 sm:flex-row sm:items-end sm:justify-end sm:px-6">
                
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex items-center gap-2">
                        <!-- Delete Selected Button -->
                        <div x-data="{ openUserMassDeleteModal: false, deleteUrl: '' }">
                            <!-- Delete Selected Button -->
                            <a href="#" 
                                x-on:click.prevent="
                                    if (selected.length > 0) { 
                                        let params = new URLSearchParams({ usernames: selected.join(',') });
                                        deleteUrl = '{{ route('be.user.mass.destroy') }}?' + params.toString();
                                        openUserMassDeleteModal = true;
                                    }
                                " 
                                :class="selected.length === 0 ? 'hidden' : ''"
                                class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-red-500 bg-red-600 text-white font-medium transition-all hover:bg-red-700 hover:border-red-600 focus:ring focus:ring-red-300 dark:bg-red-700 dark:border-red-600 dark:hover:bg-red-800">
                                <i class="bx bx-x text-lg"></i>
                                Delete Selected
                            </a>                   

                            <!-- Delete Confirmation Modal -->
                            <div x-show="openUserMassDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-[400px]">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Confirm Deletion</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        Are you sure you want to delete the selected items?
                                    </p>

                                    <div class="mt-4 flex justify-end gap-3">
                                        <button @click="openUserMassDeleteModal = false" 
                                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                            Cancel
                                        </button>
                                        <a :href="deleteUrl" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                            Yes, Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>                         

                        <!-- Reset Filter Button -->
                        <a href="{{ route('be.user.index') }}"
                            class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-gray-400 bg-gray-100 text-gray-700 font-medium transition-all hover:bg-gray-200 hover:border-gray-500 focus:ring focus:ring-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                            <i class="bx bx-reset text-lg"></i>
                            Reset Filter
                        </a>
                        
                        <!-- Filter Modal need to adjust the sort-->
                        <div x-data="{ open: false, selectedField: '{{ request()->query('filter') ? array_key_first(request('filter')) : '' }}' }">                            
                            <!-- Filter Button -->
                            <button @click.prevent="open = true"
                                class="flex items-center gap-2 h-[42px] px-4 py-2.5 rounded-lg border border-purple-500 bg-purple-600 text-white font-medium transition-all hover:bg-purple-700 hover:border-purple-600 focus:ring focus:ring-purple-300 dark:bg-purple-700 dark:border-purple-600 dark:hover:bg-purple-800">
                                <i class="bx bx-filter text-lg"></i>
                                Filter
                            </button>

                            <!-- Modal -->
                            <div x-cloak x-show="open" @keydown.escape.window="open = false"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div @click.away="open = false"
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-1/2">
                                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Filter Options</h2>

                                    <!-- Form -->
                                    <form method="GET" action="{{ route('be.user.index') }}">
                                        <!-- Role Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Role
                                            </label>
                                            <select name="role"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                <option value="">All Roles</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Limit Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Limit
                                            </label>
                                            <select name="limit"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                @foreach ($limits as $limit)
                                                    <option value="{{ $limit }}" {{ request('limit', 10) == $limit ? 'selected' : '' }}>
                                                        {{ $limit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Filter by Keyword -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Keyword
                                            </label>
                                            <input type="text" name="keyword" 
                                                value="{{ request('keyword', '') }}"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg 
                                                    bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 
                                                    focus:ring focus:ring-blue-500 focus:outline-none">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                                Anything that match in: {{ implode(', ', $allowedFilterFields) }}
                                            </span>
                                        </div>

                                        <!-- Sort Field Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sort By
                                            </label>
                                            <select name="sort_by"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                @foreach ($allowedSortFields as $field)
                                                    <option value="{{ $field }}" {{ request('sort_by') === $field ? 'selected' : '' }}>
                                                        {{ ucfirst($field) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Sort Field Selection -->
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Sort Order
                                            </label>
                                            <select name="sort_order"
                                                class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 focus:ring focus:ring-blue-500">
                                                <option value="ASC" {{ request('sort_order', 'ASC') === 'ASC' ? 'selected' : '' }}>
                                                    Ascending
                                                </option>
                                                <option value="DESC" {{ request('sort_order', 'ASC') === 'DESC' ? 'selected' : '' }}>
                                                    Descending
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Buttons -->
                                        <div class="mt-6 flex justify-end gap-3">
                                            <button type="button" @click="open = false"
                                                class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                                Apply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>            
                </div>
            </div>

            <div class="min-h-[500px] custom-scrollbar max-w-full overflow-x-auto px-5 sm:px-6">
                <table class="min-w-full table-auto">
                    <thead class="border-y border-gray-200 dark:border-gray-800 dark:bg-gray-900">
                        <tr class="text-left text-gray-600 dark:text-gray-300 text-sm">
                            <th class="w-10 px-6 py-3">
                                <input 
                                    type="checkbox" 
                                    class="flex h-5 w-5 border-gray-300 cursor-pointer items-center justify-center rounded-md border-[1.25px] transition-all"
                                    x-bind:checked="selected.length > 0 && selected.length === document.querySelectorAll('.user-checkbox').length"
                                    x-on:change="selected = $event.target.checked ? 
                                        [...document.querySelectorAll('.user-checkbox')].map(cb => cb.value) : []">
                            </th>                            
                            <th class="w-20 px-4 py-3 font-medium">No.</th>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Username</th>
                            <th class="px-4 py-3 font-medium">Email Verified At</th>
                            <th class="px-4 py-3 font-medium">Created At</th>
                            <th class="px-4 py-3 font-medium">Updated At</th>
                            <th class="px-4 py-3 font-medium text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800 dark:text-gray-400">
                        @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="w-10 px-6 py-3">
                                <input 
                                    type="checkbox"
                                    class="user-checkbox flex h-5 w-5 border-gray-300 cursor-pointer items-center justify-center rounded-md border-[1.25px] transition-all" value="{{ $user->username }}" 
                                    x-model="selected">
                            </td>
                            <td class="w-20 px-4 py-3">{{ $loop->iteration }}</td>
                            <td class="px-4 pt-4 flex items-start space-x-3">
                                <img class="h-12 w-12 rounded-full" src="{{ Avatar::create($user->name)->toBase64() }}" />
                                <div class="flex flex-col justify-center">
                                    <span class="font-medium">{{ $user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $user->email }}</span>
                                </div>
                            </td>                            
                            <td class="px-4 py-3 @if ($user->role === '[null]') ? text-gray-500 : '' @endif">{{ $user->role }}</td>
                            <td class="px-4 py-3">{{ $user->username }}</td>
                            <td class="px-4 py-3 @if ($user->formatted_email_verified_at === '[null]') ? text-gray-500 : '' @endif">{{ $user->formatted_email_verified_at }}</td>
                            <td class="px-4 py-3">{{ $user->formatted_created_at }}</td>
                            <td class="px-4 py-3">{{ $user->formatted_updated_at }}</td>
                            <td class="px-4 py-3 text-center relative">
                                <div x-cloak x-data="{ openDropDown: false }" class="inline-block">
                                    <button @click="openDropDown = !openDropDown" 
                                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                        <i class="bx bx-dots-horizontal-rounded text-xl"></i>
                                    </button>
                                    <div x-show="openDropDown" @click.outside="openDropDown = false"
                                        class="absolute right-16 top-8 mt-1 w-40 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 
                                        z-50 overflow-visible">
                                        @can(PermissionEnum::UPDATE_USER, $user)
                                        <a href="{{ route('be.user.edit', $user->username) }}" class="block w-full px-4 py-2 text-left text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                                            Edit
                                        </a>
                                        @endcan
                                        <!-- Alpine.js State Wrapper -->
                                        <div x-data="{ openUserDeleteModal: false }">
                                            <!-- Delete Button -->
                                            @can(PermissionEnum::DELETE_USER, $user)
                                            <button @click="openUserDeleteModal = true" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-800">
                                                Delete
                                            </button>
                                            @endcan

                                            <!-- Confirmation Modal -->
                                            <div x-show="openUserDeleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-[400px]">
                                                    <h2 class="text-lg text-start font-semibold text-gray-800 dark:text-gray-200">Confirm Deletion</h2>
                                                    <p class="text-sm text-start text-gray-600 dark:text-gray-400 mt-2">
                                                        Are you sure you want to delete the selected items?
                                                    </p>

                                                    <!-- Centered Buttons -->
                                                    <div class="flex justify-end space-x-3 mt-3">
                                                        <!-- Cancel Button -->
                                                        <button @click="openUserDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                                            Cancel
                                                        </button>

                                                        <!-- Delete Form -->
                                                        <form action="{{ route('be.user.destroy', $user->username) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                                Yes, Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </td>                                                                
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-gray-400">No data available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>            
                     
            <div class="{{ !$users->previousPageUrl() && !$users->nextPageUrl() ? '' : 'border-t border-gray-200 px-6 py-4 dark:border-gray-800' }}">
                <div class="flex items-center justify-between">
                    <!-- Previous Button -->
                    @if ($users->previousPageUrl())
                        <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition">
                            <span class="hidden sm:inline">Previous</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
            
                    <!-- Pagination Links - Always Centered -->
                    <div class="flex justify-center flex-1">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
            
                    <!-- Next Button -->
                    @if ($users->nextPageUrl())
                        <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 shadow-sm hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200 transition">
                            <span class="hidden sm:inline">Next</span>
                        </a>
                    @else
                        <div class="w-[96px]"></div>
                    @endif
                </div>
            </div>                             
            
        </div>
        <!-- Table Five -->
    </div>
   </div>
</main>
<!-- ===== Main Content End ===== -->
@endsection

@section('bottom-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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