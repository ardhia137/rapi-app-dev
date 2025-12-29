@extends('layouts.app')

@section('title', __('Detail Batch Produk'))

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">{{ __('Detail Batch Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">

                <h3 class="mb-4 font-semibold">{{ __('Detail Batch Produk') }}</h3>

                {{-- Barcode --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Barcode Produk</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->product->barcode ?? '-' }}
                    </div>
                </div>

                {{-- Produk --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Produk</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->product->nama_produk ?? '-' }}
                    </div>
                </div>

                {{-- Batch Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Batch Number</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->batch_number }}
                    </div>
                </div>

                {{-- Tanggal Masuk --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ \Carbon\Carbon::parse($productBatch->tanggal_masuk)->format('d-m-Y') }}
                    </div>
                </div>

                {{-- Tanggal Expired --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Expired</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->tanggal_expired 
                            ? \Carbon\Carbon::parse($productBatch->tanggal_expired)->format('d-m-Y')
                            : '-' }}
                    </div>
                </div>

                {{-- Qty Masuk --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Kuantitas Masuk</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->quantity_masuk }}
                    </div>
                </div>

                {{-- Qty Sekarang --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Kuantitas Sekarang</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->quantity_sekarang }}
                    </div>
                </div>

                {{-- Supplier --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $productBatch->supplier }}
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    @php $status = $productBatch->status; @endphp
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm
                        {{ $status === 'sold_out' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $status === 'expired' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                    ">
                        {{ $status === 'sold_out' ? 'Sold Out' : ($status === 'expired' ? 'Expired' : 'Active') }}
                    </span>
                </div>

              {{-- Buttons --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('product-batches.index') }}"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Kembali
                    </a>

                    <a href="{{ route('product-batches.edit', $productBatch->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Edit
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
