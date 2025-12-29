<!-- Desktop Sidebar -->
<aside :class="sidebarCollapsed ? 'w-20' : 'w-64'"
       class="hidden md:block bg-white text-slate-700 shadow-2xl transition-all duration-300 border-r border-slate-200">
    <div class="h-full flex flex-col">
        <!-- Logo/Brand Section -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200">
            <div class="flex items-center gap-3 flex-1">
                <div class="w-16 h-16 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('images/logo-rapi.png') }}" alt="Logo" class="h-16 w-16 object-contain" />
                </div>
                <div x-show="!sidebarCollapsed" class="leading-tight flex-1">
                  <div class="text-lg font-bold tracking-tight text-slate-800">RAPI</div>
                    <div class="text-xs text-slate-500 font-medium">Plafon System</div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 flex-1 overflow-auto">
            <ul class="space-y-1.5">

                <!-- Dashboard -->
                <li>
                   <a href="{{ route('dashboard') }}"
                    title="Dashboard"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="font-medium">Dashboard</span>
                    </a>
                </li>

                <!-- Users -->
                @can('users.view')
                <li>
                   <a href="{{ route('users.index') }}"
                    title="Users"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                        :class="sidebarCollapsed ? 'justify-center' : ''">
                            <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </span>
                            <span x-show="!sidebarCollapsed" class="font-medium">Pengguna</span>
                        </a>
                </li>
                @endcan

                <!-- Roles & Permissions -->
                @can('roles.view')
                <li x-data="{ open: {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                        :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" class="flex-1 text-start font-medium">Peran & Izin</span>
                    <svg x-show="!sidebarCollapsed"
                        :class="open ? 'rotate-90' : ''"
                        class="h-4 w-4 transform transition-transform text-slate-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('roles.view')
                        <li>
                            <a href="{{ route('roles.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Peran
                            </a>
                        </li>
                        @endcan
                        @can('permissions.view')
                        <li>
                            <a href="{{ route('permissions.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('permissions.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Izin
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Master Products -->
                @canany(['products.view', 'product-batches.view'])
               <li x-data="{ open: {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'true' : 'false' }} }" class="relative">
                <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                        :class="sidebarCollapsed ? 'justify-center' : ''">
                    <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*')  ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </span>
                    <span x-show="!sidebarCollapsed" class="flex-1 text-start font-medium">Master Produk</span>
                    <svg x-show="!sidebarCollapsed"
                        :class="open ? 'rotate-90' : ''"
                        class="h-4 w-4 transform transition-transform text-slate-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                  <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('products.view')
                        <li>
                            <a href="{{ route('products.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('products.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Produk
                            </a>
                        </li>
                        @endcan
                        @can('product-batches.view')
                        <li>
                            <a href="{{ route('product-batches.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('product-batches.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Batch Produk</a>
                        </li>
                        @endcan
                       
                    </ul>
                </li>
                @endcan

                <!-- Customers -->
                @can('customers.view')
                <li>
                  <a href="{{ route('customers.index') }}"
                    title="Customers"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="font-medium">Pelanggan</span>
                    </a>
                </li>
                @endcan

               <!-- Master Penjualan -->
                @can('invoices.view')
                <li x-data="{ open: {{ request()->routeIs('invoices.*', 'surat-jalan.*', 'riwayat-penjualan.*', 'monthly-targets.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                            :aria-expanded="open.toString()"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('invoices.*', 'surat-jalan.*', 'riwayat-penjualan.*', 'monthly-targets.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('invoices.*', 'surat-jalan.*', 'riwayat-penjualan.*', 'monthly-targets.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="flex-1 text-start font-medium">Master Penjualan</span>
                        <svg x-show="!sidebarCollapsed"
                            :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('monthly-target.view')
                        <li>
                            <a href="{{ route('monthly-targets.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('monthly-targets.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Target Bulanan</a>
                        </li>
                        @endcan
                        @can('invoices.view')
                        <li>
                            <a href="{{ route('invoices.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('invoices.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Penjualan</a>
                        </li>
                        @endcan
                        @can('invoices.setor')
                        <li>
                            <a href="{{ route('invoices.setor') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('invoices.setor') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Setor Penjualan</a>
                        </li>
                        @endcan
                        @can('surat-jalan.view')
                        <li>
                            <a href="{{ route('surat-jalan.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('surat-jalan.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Surat Jalan</a>
                        </li>
                        @endcan
                        @can('transactions.view')
                        <li>
                            <a href="{{ route('riwayat-penjualan.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('riwayat-penjualan.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Riwayat Transaksi</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Master Finance -->
                 @can('finance.input.view')
                <li x-data="{ open: {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                            :aria-expanded="open.toString()"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}"
                            :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="flex-1 text-start font-medium">Master Keuangan</span>
                        <svg x-show="!sidebarCollapsed"
                            :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                         @can('budget-target.view')
                        <li>
                            <a href="{{ route('budget-target.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('budget-target.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Target Anggaran
                            </a>
                        </li>
                        @endcan
                        @can('finance.input.view')
                        <li>
                            <a href="{{ route('finance-records.index') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('finance-records.index') || request()->routeIs('finance-records.create') || request()->routeIs('finance-records.edit') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Input Keuangan
                            </a>
                        </li>
                        @endcan
                        @can('finance.history')
                        <li>
                            <a href="{{ route('finance-records.history') }}"
                            class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('finance-records.history') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Riwayat Keuangan
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Divider -->
                <li class="pt-4">
                    <div class="border-t border-white/10"></div>
                </li>

                <!-- Logout -->
                <li>
                    <form action="{{ route('logout') }}" method="POST" data-logout-confirm>
                        @csrf
                       <button type="submit"
                            title="Logout"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group text-slate-700 hover:bg-red-50"
                            :class="sidebarCollapsed ? 'justify-center' : ''">
                        <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-red-100 transition-all duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                        </span>
                        <span x-show="!sidebarCollapsed" class="font-medium">Logout</span>
                    </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>

{{-- Mobile Sidebar Overlay --}}
<div x-show="mobileOpen"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="mobileOpen = false"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden"
     style="display: none;">
</div>

{{-- Mobile Sidebar --}}
<aside x-show="mobileOpen"
       x-transition:enter="transition ease-in-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in-out duration-300 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       class="fixed inset-y-0 left-0 w-64 bg-white text-slate-700 shadow-2xl z-50 md:hidden overflow-y-auto border-r border-slate-200"
       style="display: none;">
    <div class="h-full flex flex-col">
        <!-- Mobile Header with Close Button -->
        <div class="flex items-center justify-between p-4 border-b border-slate-200">
            <div class="flex items-center gap-3 flex-1">
                <div class="w-16 h-16 rounded-lg flex items-center justify-center">
                    <img src="{{ asset('images/logo-rapi.png') }}" alt="Logo" class="h-16 w-16 object-contain" />
                </div>
                <div class="leading-tight flex-1">
                    <div class="text-lg font-bold tracking-tight text-slate-800">RAPI</div>
                    <div class="text-xs text-slate-500 font-medium">Plafon System</div>
                </div>
            </div>
            <button @click="mobileOpen = false" class="p-2 rounded-lg hover:bg-slate-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <nav class="p-4 flex-1 overflow-auto">
            <ul class="space-y-1.5">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                        title="Dashboard"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                        </span>
                        <span class="font-medium">Dashboard</span>
                    </a>
                </li>

                <!-- Users -->
                @can('users.view')
                <li>
                    <a href="{{ route('users.index') }}"
                        title="Users"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </span>
                        <span class="font-medium">Pengguna</span>
                    </a>
                </li>
                @endcan

                <!-- Roles & Permissions -->
                @can('roles.view')
                <li x-data="{ open: {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </span>
                        <span class="flex-1 text-start font-medium">Peran & Izin</span>
                        <svg :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('roles.view')
                        <li>
                            <a href="{{ route('roles.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Peran
                            </a>
                        </li>
                        @endcan
                        @can('permissions.view')
                        <li>
                            <a href="{{ route('permissions.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('permissions.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Izin
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Master Products -->
                @canany(['products.view', 'product-batches.view'])
                <li x-data="{ open: {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('products.*') || request()->routeIs('product-batches.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </span>
                        <span class="flex-1 text-start font-medium">Master Produk</span>
                        <svg :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('products.view')
                        <li>
                            <a href="{{ route('products.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('products.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Produk
                            </a>
                        </li>
                        @endcan
                        @can('product-batches.view')
                        <li>
                            <a href="{{ route('product-batches.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('product-batches.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Batch Produk
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Customers -->
                @can('customers.view')
                <li>
                    <a href="{{ route('customers.index') }}"
                        title="Customers"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('customers.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </span>
                        <span class="font-medium">Pelanggan</span>
                    </a>
                </li>
                @endcan

                <!-- Master Penjualan -->
                @can('invoices.view')
                <li x-data="{ open: {{ request()->routeIs('invoices.*', 'surat-jalan.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('invoices.*', 'surat-jalan.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('invoices.*', 'surat-jalan.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                            </svg>
                        </span>
                        <span class="flex-1 text-start font-medium">Master Penjualan</span>
                        <svg :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                     @can('monthly-target.view')
                        <li>
                            <a href="{{ route('monthly-targets.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('monthly-targets.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">Target Bulanan</a>
                        </li>   
                        @endcan
                    @can('invoices.view')
                        <li>
                            <a href="{{ route('invoices.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('invoices.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Penjualan
                            </a>
                        </li>
                        @endcan
                        @can('invoices.setor')
                        <li>
                            <a href="{{ route('invoices.setor') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('invoices.setor') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Setor Penjualan
                            </a>
                        </li>
                        @endcan
                        @can('surat-jalan.view')
                        <li>
                            <a href="{{ route('surat-jalan.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('surat-jalan.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Surat Jalan
                            </a>
                        </li>
                        @endcan
                        @can('transactions.view')
                        <li>
                            <a href="{{ route('riwayat-penjualan.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('riwayat-penjualan.index') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Riwayat Transaksi
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Master Finance -->
                @can('finance.input.view')
                <li x-data="{ open: {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'true' : 'false' }} }" class="relative">
                    <button @click.prevent="open = !open"
                        :aria-expanded="open.toString()"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                        <span class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 {{ request()->routeIs('budget-target.*') || request()->routeIs('finance-records.*') ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="flex-1 text-start font-medium">Master Keuangan</span>
                        <svg :class="open ? 'rotate-90' : ''"
                            class="h-4 w-4 transform transition-transform text-slate-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <ul x-cloak x-show="open" x-transition class="mt-1.5 space-y-1 pl-12" style="display:none;">
                        @can('budget-target.view')
                        <li>
                            <a href="{{ route('budget-target.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('budget-target.*') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Target Anggaran
                            </a>
                        </li>
                        @endcan
                        @can('finance.input.view')
                        <li>
                            <a href="{{ route('finance-records.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('finance-records.index') || request()->routeIs('finance-records.create') || request()->routeIs('finance-records.edit') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Input Keuangan
                            </a>
                        </li>
                        @endcan
                        @can('finance.history')
                        <li>
                            <a href="{{ route('finance-records.history') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('finance-records.history') ? 'bg-blue-50 text-blue-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
                                Riwayat Keuangan
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                <!-- Divider -->
                <li class="pt-4">
                    <div class="border-t border-slate-200"></div>
                </li>

                <!-- Logout -->
                <li>
                    <form action="{{ route('logout') }}" method="POST" data-logout-confirm>
                        @csrf
                        <button type="submit"
                            title="Logout"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 group text-slate-700 hover:bg-red-50">
                            <span class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-red-100 transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                            </span>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>