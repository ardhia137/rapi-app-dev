@extends('layouts.app')

@section('title', __('Dashboard'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Dashboard') }}</h2>
@endsection
@section('content')
<div class="py-2 overflow-x-hidden">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-6 overflow-x-hidden">

        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 shadow-lg rounded-2xl p-4 sm:p-6 text-white overflow-hidden">
            <div class="flex items-center justify-between min-w-0">
                <div class="min-w-0 flex-1">
                    <h3 class="text-lg sm:text-2xl font-bold truncate">Selamat Datang, {{ $user->name }}! 👋</h3>
                    <p class="text-indigo-100 mt-1 text-sm sm:text-base truncate">{{ $user->email }}</p>
                    <p class="text-xs sm:text-sm text-indigo-100 mt-2 truncate">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
                <div class="hidden md:block flex-shrink-0">
                    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 overflow-x-hidden">
            <!-- Month Filter -->
            <div class="md:col-span-2 lg:col-span-4 overflow-x-hidden">
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-end gap-3 justify-end" id="monthFilterForm">
                    <div class="min-w-0">
                        <input type="month" name="month" value="{{ $filterMonth ?? now()->format('Y-m') }}" class="px-3 py-2.5 border rounded-lg text-sm sm:text-base w-full max-w-full" id="monthFilterInput">
                    </div>
                    
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" id="scanModeToggle" class="px-3 py-2.5 rounded-lg text-sm border transition-colors bg-white hover:bg-gray-50 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full" id="scanModeIndicator"></span>
                            <span id="scanModeText">Scan Mode: OFF</span>
                        </button>
                    </div>
                    
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        const input = document.getElementById('monthFilterInput');
                        const form = document.getElementById('monthFilterForm');
                        if (input && form) {
                            input.addEventListener('change', function(){
                                form.submit();
                            });
                        }
                    });
                </script>
            </div>
            <!-- Total Produk -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 sm:p-6 border border-gray-100 overflow-hidden min-w-0">
                <div class="flex items-center justify-between gap-3 min-w-0">
                    <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Produk</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2 truncate">{{ $totalProducts }}</p>
                    <p class="text-xs {{ $productGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $productGrowth >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                        </svg>
                        <span class="truncate">{{ $productGrowth >= 0 ? '+' : '' }}{{ number_format(abs($productGrowth), 1) }}% dari bulan lalu</span>
                    </p>
                </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Customer -->
           <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 sm:p-6 border border-gray-100 overflow-hidden min-w-0">
                <div class="flex items-center justify-between gap-3 min-w-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Customer</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2 truncate">{{ number_format($totalCustomers) }}</p>
                        <p class="text-xs {{ $customerGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($customerGrowth >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            <span class="truncate">{{ $customerGrowth >= 0 ? '+' : '' }}{{ number_format(abs($customerGrowth), 1) }}% dari bulan lalu</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Invoice -->
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 sm:p-6 border border-gray-100 overflow-hidden min-w-0">
                <div class="flex items-center justify-between gap-3 min-w-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Invoice</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800 mt-2 truncate">{{ number_format($totalInvoices) }}</p>
                        <p class="text-xs {{ $invoiceGrowth >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2 flex items-center">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($invoiceGrowth >= 0)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                @endif
                            </svg>
                            <span class="truncate">{{ $invoiceGrowth >= 0 ? '+' : '' }}{{ number_format(abs($invoiceGrowth), 1) }}% dari bulan lalu</span>
                        </p>
                    </div>
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 p-4 sm:p-6 border border-gray-100 overflow-hidden min-w-0">
            <div class="flex items-center justify-between gap-3 min-w-0">
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-medium text-gray-600 truncate">Total Pemasukan (Paid)</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 mt-2 truncate">
                        Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-2 truncate">
                        Dari {{ number_format($paidCount ?? 0, 0, ',', '.') }} invoice paid
                    </p>
                </div>
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        </div>

        <!-- Charts Row -->
        <div class="overflow-x-hidden">

            <!-- Top 5 Produk Terlaris -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Top 5 Produk Terlaris</h3>
                    <p class="text-xs text-gray-500 mt-1 truncate">Berdasarkan Quantity Terjual</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($topProductsData['labels'] as $index => $productName)
                        @php
                            $quantity = $topProductsData['quantities'][$index] ?? 0;
                            $gradients = [
                                'from-blue-500 to-indigo-500',
                                'from-purple-500 to-pink-500',
                                'from-pink-500 to-rose-500',
                                'from-orange-500 to-amber-500',
                                'from-green-500 to-emerald-500'
                            ];
                            $gradient = $gradients[$index] ?? 'from-gray-500 to-gray-600';

                            $bgColors = [
                                'bg-blue-100',
                                'bg-purple-100',
                                'bg-pink-100',
                                'bg-orange-100',
                                'bg-green-100'
                            ];
                            $textColors = [
                                'text-blue-600',
                                'text-purple-600',
                                'text-pink-600',
                                'text-orange-600',
                                'text-green-600'
                            ];
                            $bgColor = $bgColors[$index] ?? 'bg-gray-100';
                            $textColor = $textColors[$index] ?? 'text-gray-600';
                        @endphp
                        <div class="p-3 sm:p-4 hover:bg-gray-50 transition-colors overflow-hidden">
                            <div class="flex items-center justify-between gap-3 min-w-0">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br {{ $gradient }} rounded-lg flex items-center justify-center text-white font-bold text-base sm:text-lg flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-sm sm:text-base text-gray-800 truncate">{{ $productName }}</p>
                                        <p class="text-xs sm:text-sm text-gray-500 truncate">Produk Terlaris #{{ $index + 1 }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="flex items-center gap-2">
                                         <div class="text-right">
                                            <p class="font-bold text-gray-800 text-sm sm:text-base lg:text-lg whitespace-nowrap">{{ number_format($quantity) }}</p>
                                            <p class="text-xs text-gray-500 whitespace-nowrap">Unit Terjual</p>
                                        </div>
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 {{ $bgColor }} rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 {{ $textColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p>Belum ada data penjualan produk</p>
                        </div>
                    @endforelse
                </div>
                @if(count($topProductsData['labels'] ?? []) > 0)
                <div class="p-4 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat Semua Produk →
                    </a>
                </div>
                @endif
            </div>

        </div>

        <!-- Recent Activities & Top Customers -->
       <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 overflow-x-hidden">

            <!-- Recent Invoices -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Invoice Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentInvoices as $invoice)
                        <div class="p-3 sm:p-4 hover:bg-gray-50 transition-colors overflow-hidden">
                            <div class="flex items-center justify-between gap-3 min-w-0">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs sm:text-sm font-semibold text-blue-600">INV</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-sm sm:text-base text-gray-800 truncate">{{ $invoice->invoice_number }}</p>
                                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $invoice->customer->nama_customer ?? 'Customer' }}</p>
                                    </div>
                                </div>
                               <div class="text-right flex-shrink-0">
                                <p class="font-semibold text-sm sm:text-base text-gray-800 whitespace-nowrap">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
                                @if($invoice->status_pembayaran == 'paid')
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full mt-1 whitespace-nowrap">Lunas</span>
                                @elseif($invoice->status_pembayaran == 'unpaid')
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full mt-1 whitespace-nowrap">Belum Lunas</span>
                                @elseif($invoice->status_pembayaran == 'overdue')
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700 rounded-full mt-1 whitespace-nowrap">Terlambat</span>
                                @elseif($invoice->status_pembayaran == 'cancelled')
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full mt-1 whitespace-nowrap">Dibatalkan</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded-full mt-1 whitespace-nowrap">{{ ucfirst($invoice->status_pembayaran) }}</span>
                                @endif
                            </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 sm:p-8 text-center text-gray-500">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm sm:text-base">Belum ada invoice</p>
                        </div>
                    @endforelse
                </div>
                @if($recentInvoices->count() > 0)
                <div class="p-3 sm:p-4 border-t border-gray-100">
                    <a href="{{ route('invoices.index') }}" class="text-xs sm:text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat Semua Invoice →
                    </a>
                </div>
                @endif
            </div>

            <!-- Top Customers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 truncate">Customer Terbaik</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($topCustomers as $index => $customer)
                        @php
                            $gradients = [
                                'from-blue-500 to-purple-500',
                                'from-purple-500 to-pink-500',
                                'from-pink-500 to-red-500',
                                'from-orange-500 to-yellow-500',
                                'from-green-500 to-teal-500'
                            ];
                            $gradient = $gradients[$index] ?? 'from-gray-500 to-gray-600';
                        @endphp
                        <div class="p-3 sm:p-4 hover:bg-gray-50 transition-colors overflow-hidden">
                            <div class="flex items-center justify-between gap-3 min-w-0">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br {{ $gradient }} rounded-lg flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-sm sm:text-base text-gray-800 truncate">{{ $customer->nama_customer }}</p>
                                        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $customer->no_hp ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-semibold text-sm sm:text-base text-yellow-600 whitespace-nowrap">
                                        {{ number_format($customer->point ?? 0) }} pts
                                    </p>
                                    <p class="text-xs text-gray-500 whitespace-nowrap">Total Point</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 sm:p-8 text-center text-gray-500">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm sm:text-base">Belum ada data customer</p>
                        </div>
                    @endforelse
                </div>
                @if($topCustomers->count() > 0)
                <div class="p-3 sm:p-4 border-t border-gray-100">
                    <a href="{{ route('customers.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                        Lihat Semua Customer →
                    </a>
                </div>
                @endif
            </div>

        </div>

        <!-- Scan Result Modal -->
        <div id="scanModal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="relative max-w-lg mx-auto mt-24 bg-white rounded-xl shadow-xl overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800">Hasil Scan Produk</h3>
                    <button class="text-gray-500 hover:text-gray-700" id="scanModalClose" aria-label="Close">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 sm:p-6" id="scanModalBody">
                    <div class="text-center text-gray-500">Memuat data produk...</div>
                </div>
                <div class="p-4 border-t border-gray-100 flex justify-end gap-2">
                    <a id="scanModalAddStock" href="#" class="px-3 py-2 rounded-lg text-sm bg-indigo-600 text-white hover:bg-indigo-700">Tambah Stok</a>
                    <a id="scanModalGoToSales" href="#" class="px-3 py-2 rounded-lg text-sm bg-green-600 text-white hover:bg-green-700">Penjualan</a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100 overflow-hidden">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4 truncate">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

                <!-- Tambah Invoice -->
                @can('finance.input.create')
                <a href="{{ route('invoices.create') }}" class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg hover:bg-gray-50 transition-colors group overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center truncate w-full">Tambah Invoice</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg opacity-50 cursor-not-allowed overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-400 text-center truncate w-full">Tambah Invoice</span>
                </div>
                @endcan

                <!-- Tambah Customer -->
                @can('customers.create')
                <a href="{{ route('customers.create') }}" class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg hover:bg-gray-50 transition-colors group overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center truncate w-full">Tambah Customer</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg opacity-50 cursor-not-allowed overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-400 text-center truncate w-full">Tambah Customer</span>
                </div>
                @endcan

                <!-- Tambah Produk -->
                @can('products.create')
                <a href="{{ route('products.create') }}" class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg hover:bg-gray-50 transition-colors group overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center truncate w-full">Tambah Produk</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg opacity-50 cursor-not-allowed overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-400 text-center truncate w-full">Tambah Produk</span>
                </div>
                @endcan

                <!-- Lihat Laporan Keuangan -->
                @can('finance.history')
                <a href="{{ route('finance-records.history') }}" class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg hover:bg-gray-50 transition-colors group overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-700 text-center truncate w-full">Laporan Keuangan</span>
                </a>
                @else
                <div class="flex flex-col items-center gap-2 p-3 sm:p-4 rounded-lg opacity-50 cursor-not-allowed overflow-hidden">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs sm:text-sm font-medium text-gray-400 text-center truncate w-full">Laporan Keuangan</span>
                </div>
                @endcan

            </div>
        </div>

    </div>
</div>
@endsection


@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Top Customers Chart - Bar Chart
    const topCustomersCtx = document.getElementById('topCustomersChart').getContext('2d');

    @php
        $customerLabels = $topCustomersChart['labels'] ?? [];
        $customerPoints = $topCustomersChart['points'] ?? [];
    @endphp

    new Chart(topCustomersCtx, {
        type: 'bar',
        data: {
            labels: @json($customerLabels),
            datasets: [{
                label: 'Total Point',
                data: @json($customerPoints),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderColor: [
                    'rgb(99, 102, 241)',
                    'rgb(168, 85, 247)',
                    'rgb(236, 72, 153)',
                    'rgb(251, 146, 60)',
                    'rgb(34, 197, 94)'
                ],
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Point: ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });

    // Invoice Status Chart - Doughnut Chart
 const invoiceCtx = document.getElementById('invoiceChart').getContext('2d');

    @php
        $invoicePaid = $invoiceStatusData['paid'] ?? 0;
        $invoiceUnpaid = $invoiceStatusData['unpaid'] ?? 0;
        $invoiceOverdue = $invoiceStatusData['overdue'] ?? 0;
        $invoiceCancelled = $invoiceStatusData['cancelled'] ?? 0;
        $totalInvoiceChart = $invoicePaid + $invoiceUnpaid + $invoiceOverdue + $invoiceCancelled;
    @endphp

    new Chart(invoiceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Unpaid', 'Overdue', 'Cancelled'],
            datasets: [{
                data: [
                    {{ $invoicePaid }},
                    {{ $invoiceUnpaid }},
                    {{ $invoiceOverdue }},
                    {{ $invoiceCancelled }}
                ],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',   // Green untuk Paid
                    'rgba(251, 191, 36, 0.8)',  // Yellow untuk Unpaid
                    'rgba(239, 68, 68, 0.8)',   // Red untuk Overdue
                    'rgba(156, 163, 175, 0.8)'  // Gray untuk Cancelled
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)',
                    'rgb(156, 163, 175)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = {{ $totalInvoiceChart }};
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;

                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' invoice';

                            const total = {{ $totalInvoiceChart }};
                            if (total > 0) {
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                label += ' (' + percentage + '%)';
                            }

                            return label;
                        }
                    }
                }
            }
        }
    });
   const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');

    @php
        $topProductsLabels = $topProductsData['labels'] ?? [];
        $topProductsQuantities = $topProductsData['quantities'] ?? [];
    @endphp

    new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: @json($topProductsLabels),
            datasets: [{
                label: 'Quantity Terjual',
                data: @json($topProductsQuantities),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(34, 197, 94, 0.8)'
                ],
                borderColor: [
                    'rgb(99, 102, 241)',
                    'rgb(139, 92, 246)',
                    'rgb(236, 72, 153)',
                    'rgb(251, 146, 60)',
                    'rgb(34, 197, 94)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y', // Horizontal bar
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Terjual: ' + context.parsed.x + ' unit';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<script>
    // --- Global Keyboard-Wedge Scan Mode ---
    (function(){
        const toggleBtn = document.getElementById('scanModeToggle');
        const indicator = document.getElementById('scanModeIndicator');
        const textEl = document.getElementById('scanModeText');
        const modal = document.getElementById('scanModal');
        const modalBody = document.getElementById('scanModalBody');
        const modalClose = document.getElementById('scanModalClose');
        const modalDismiss = document.getElementById('scanModalDismiss');
        const addStockBtn = document.getElementById('scanModalAddStock');
        const goToSalesBtn = document.getElementById('scanModalGoToSales');

        let scanMode = false;
        let buffer = '';
        let timeoutId = null;
        let busy = false; // avoid re-entrant while modal open

        function setIndicator(active){
            if (!indicator || !textEl) return;
            indicator.className = 'inline-block w-2 h-2 rounded-full ' + (active ? 'bg-green-500' : 'bg-gray-400');
            textEl.textContent = 'Scan Mode: ' + (active ? 'ON' : 'OFF');
        }
        setIndicator(false);

        function openModal(){
            if (!modal) return;
            modal.classList.remove('hidden');
            busy = true;
        }
        function closeModal(){
            if (!modal) return;
            modal.classList.add('hidden');
            busy = false;
        }

        function finalizeBuffer(){
            const code = buffer.trim();
            buffer = '';
            if (!code || code.length < 6) return; // minimal barcode length guard
            // sanitize to alnum
            const cleaned = code.replace(/[^A-Za-z0-9]/g, '');
            // fetch product
            openModal();
            if (modalBody) modalBody.innerHTML = '<div class="text-center text-gray-500">Memuat data produk...</div>';
            fetch("{{ route('scan.product') }}" + '?' + new URLSearchParams({code: cleaned}), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async (res) => {
                const data = await res.json().catch(() => ({ ok:false, message:'Gagal parse respons' }));
                if (!res.ok || !data.ok) {
                    const msg = (data && data.message) ? data.message : 'Produk tidak ditemukan';
                    if (modalBody) modalBody.innerHTML = '<div class="text-center text-red-600">' + msg + '</div>';
                    viewProductBtn && (viewProductBtn.classList.add('hidden'));
                    return;
                }
                const p = data.data;
                const detailHtml = `
                    <div class="flex items-start gap-4">
                        <!-- FOTO -->
                        <div class="w-20 h-20 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                            ${p.foto_url 
                                ? `<img src="${p.foto_url}" alt="${p.nama_produk}" class="w-full h-full object-cover">` 
                                : ''
                            }
                        </div>

                        <!-- INFO -->
                        <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-1 min-w-0">
                            <!-- KIRI -->
                            <div class="col-span-2 text-lg font-semibold text-gray-800 truncate">
                                ${p.nama_produk ?? 'Produk'}
                            </div>

                            <div class="text-sm text-gray-600 truncate">
                                <span class="font-medium">Kategori:</span> ${p.kategori ?? '-'}
                            </div>

                            <div class="text-sm text-gray-600 truncate">
                                <span class="font-medium">Satuan:</span> ${p.satuan ?? '-'}
                            </div>

                            <!-- KANAN -->
                            <div class="text-sm text-gray-800">
                                <span class="font-medium">Harga:</span>
                                Rp ${new Intl.NumberFormat('id-ID').format(p.harga ?? 0)}
                            </div>

                            <div class="text-sm text-gray-800">
                                <span class="font-medium">Stok:</span> ${p.stok ?? 0}
                            </div>
                        </div>
                    </div>

                `;
                if (modalBody) modalBody.innerHTML = detailHtml;
                const batchCreateUrl = "{{ route('product-batches.create') }}";
                const invoiceCreateUrl = "{{ route('invoices.create') }}";
                if (addStockBtn) {
                    const qs = new URLSearchParams({ barcode: (p.barcode || '') }).toString();
                    addStockBtn.href = batchCreateUrl + '?' + qs;
                    addStockBtn.classList.remove('hidden');
                }
                if (goToSalesBtn) {
                    const qs2 = new URLSearchParams({ barcode: (p.barcode || '') }).toString();
                    goToSalesBtn.href = invoiceCreateUrl + '?' + qs2;
                    goToSalesBtn.classList.remove('hidden');
                }
            })
            .catch(() => {
                if (modalBody) modalBody.innerHTML = '<div class="text-center text-red-600">Terjadi kesalahan jaringan</div>';
                addStockBtn && (addStockBtn.classList.add('hidden'));
                goToSalesBtn && (goToSalesBtn.classList.add('hidden'));
            });
        }

        document.addEventListener('keydown', function(ev){
            if (!scanMode) return;
            if (busy) return;
            const t = ev.target;
            const isEditable = (t && (t.isContentEditable || ['INPUT','TEXTAREA','SELECT'].includes(t.tagName)));
            if (isEditable) return; // don't hijack normal input

            // Typical scanners send fast bursts ending with Enter
            if (ev.key === 'Enter') {
                ev.preventDefault();
                finalizeBuffer();
                return;
            }

            const key = ev.key;
            if (!/^[A-Za-z0-9]$/.test(key)) return;
            buffer += key;
            // If no key comes within 120ms, finalize (fallback if scanner not sending Enter)
            clearTimeout(timeoutId);
            timeoutId = setTimeout(finalizeBuffer, 120);
        }, true);

        function toggleScanMode(){
            scanMode = !scanMode;
            setIndicator(scanMode);
        }

        if (toggleBtn){
            toggleBtn.addEventListener('click', function(){
                toggleScanMode();
            });
        }

        // Modal controls
        [modalClose, modalDismiss].forEach(btn => btn && btn.addEventListener('click', function(){
            closeModal();
        }));
        // Close on backdrop click
        modal && modal.addEventListener('click', function(e){
            if (e.target === modal) closeModal();
        });
    })();
</script>
@endpush
