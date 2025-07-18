<!-- ===== Sidebar Start ===== -->
<aside
   :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
   class="sidebar fixed left-0 top-0 z-40 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0"
>
<!-- SIDEBAR HEADER -->
<div
      :class="sidebarToggle ? 'justify-center' : 'justify-between'"
      class="flex items-center gap-2 pt-8 sidebar-header pb-7"
   >
    <a href="{{ route('be.dashboard.index') }}">
      <span class="logo" :class="sidebarToggle ? 'hidden' : ''">
        <!-- Light mode logo -->
        <div class="flex items-center space-x-2 dark:hidden">
          <img
              class="h-10 w-auto rounded"
              src="{{ asset('logo/iamelse-logo-1.png') }}"
              alt="Logo"
          />
          <span class="ps-1 text-3xl font-bold text-gray-900 dark:text-white">
            Iamelse
          </span>
        </div>

        <!-- Dark mode logo -->
        <div class="flex items-center space-x-2 hidden dark:flex">
          <img
              class="h-10 w-auto rounded"
              src="{{ asset('logo/iamelse-logo-1.png') }}"
              alt="Logo"
          />
          <span class="ps-1 text-3xl font-bold text-gray-900 dark:text-white">
            Iamelse
          </span>
        </div>
      </span>

      <!-- Logo icon when sidebar is toggled -->
      <img
          class="logo-icon hidden lg:block rounded h-10 w-10"
          :class="{ 'lg:block': sidebarToggle, 'lg:hidden': !sidebarToggle }"
          src="{{ asset('logo/iamelse-logo-1.png') }}"
          alt="Logo"
      />
    </a>
</div>
<!-- SIDEBAR HEADER -->
<div
   class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
>
   <!-- Sidebar Menu -->
   <nav>
      <!-- Menu Group -->
      <div>
         @php
            use App\Enums\PermissionEnum;

            $menus = collect([
                [
                    'title' => 'Main',
                    'order' => 1,
                    'children' => [
                        [
                            'order' => 1,
                            'active' => 'be.dashboard',
                            'route' => 'be.dashboard.index',
                            'icon' => 'bx-line-chart',
                            'label' => 'Dashboard',
                            'permission' => PermissionEnum::READ_DASHBOARD
                        ],
                        [
                            'order' => 2,
                            'active' => 'be.sale',
                            'route' => 'be.sale.index',
                            'icon' => 'bx-wallet',
                            'label' => 'Sale',
                            'permission' => PermissionEnum::READ_SALE
                        ],
                        [
                            'order' => 3,
                            'active' => 'be.item',
                            'route' => 'be.item.index',
                            'icon' => 'bx-package',
                            'label' => 'Item',
                            'permission' => PermissionEnum::READ_ITEM
                        ],
                    ]
                ],
                [
                    'title' => 'Settings',
                    'order' => 99,
                    'children' => [
                        [
                            'order' => 1,
                            'active' => 'be.role.and.permission',
                            'route' => 'be.role.and.permission.index',
                            'icon' => 'bx-lock-open',
                            'label' => 'Role & Permissions',
                            'permission' => PermissionEnum::READ_ROLE
                        ],
                        [
                            'order' => 2,
                            'active' => [
                                'be.user.index',
                                'be.user.create',
                                'be.user.edit'
                            ],
                            'exact' => true,
                            'route' => 'be.user.index',
                            'icon' => 'bx bx-user',
                            'label' => 'Users',
                            'permission' => PermissionEnum::READ_USER
                        ],
                    ]
                ]
            ]);

            $userPermissions = Auth::user()->permissions;

            // Filter and sort menus based on user permissions
            $filteredMenus = $menus->map(function ($menu) use ($userPermissions) {
               $menu['children'] = collect($menu['children'])
                  ->filter(fn($child) => Auth::user()->can($child['permission'], $userPermissions))
                  ->sortBy('order'); // Sort children
               return $menu;
            })->filter(fn($menu) => $menu['children']->isNotEmpty()) // Remove empty parents
            ->sortBy('order'); // Sort parents
         @endphp

         <div class="mt-5 lg:mt-0">
            @foreach ($filteredMenus as $menu)
               <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
                  {{ $menu['title'] }}
               </h3>
               <ul class="flex flex-col gap-4 mb-6">
                  @foreach ($menu['children'] as $child)
                     @php
                           $isActive = is_array($child['active'])
                              ? collect($child['active'])->some(fn($route) => request()->routeIs($route . ($child['exact'] ?? false ? '' : '*')))
                              : request()->routeIs($child['active'] . ($child['exact'] ?? false ? '' : '*'));
                     @endphp
                     <li>
                        <a href="{{ route($child['route']) }}"
                           class="menu-item group {{ $isActive ? 'menu-item-active' : 'menu-item-inactive' }}">
                           <i class="bx bx-sm {{ $child['icon'] }}"></i>
                           {{ $child['label'] }}
                        </a>
                     </li>
                  @endforeach
               </ul>
            @endforeach
         </div>
      </div>
      <!-- Menu Group -->
   </nav>
   <!-- Sidebar Menu -->
</div>
</aside>
<!-- ===== Sidebar End ===== -->
