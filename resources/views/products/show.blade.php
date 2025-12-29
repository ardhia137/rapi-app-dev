@extends('layouts.app')

@section('title', __('Detail Produk'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

            <h3 class="mb-6 font-semibold text-lg">{{ __('Detail Produk') }}</h3>

            {{-- FOTO --}}
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700">Foto Produk</label>
                <img
                    src="{{ asset('storage/' . $product->foto_produk) }}"
                    class="mt-2 w-40 h-40 object-cover rounded-md border"
                    alt="Foto Produk"
                >
            </div>

            {{-- DETAIL PRODUK --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">

                {{-- KIRI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $product->nama_produk }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga Jual</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        Rp {{ number_format($product->harga, 0, ',', '.') }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Barcode</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $product->barcode }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Harga Beli</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        @if($product->latestBatch)
                            Rp {{ number_format($product->latestBatch->harga_beli, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">Belum ada batch</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $product->satuan }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Keuntungan</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        @if($product->latestBatch)
                            Rp {{ number_format($product->harga - $product->latestBatch->harga_beli, 0, ',', '.') }}
                        @else
                            <span class="text-gray-400">Belum ada batch</span>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $product->kategori }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Min Stok Alert</label>
                    <div class="mt-1 p-2 border rounded-md bg-gray-100">
                        {{ $product->min_stok_alert }}
                    </div>
                </div>

                {{-- STATUS --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-1">
                        @php
                            $statusLabel = $product->status === 'available' ? 'Tersedia' : 'Tidak Tersedia';
                        @endphp
                        @if($product->batches->sum('quantity_sekarang') >= $product->min_stok_alert)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded text-sm">
                                {{ $statusLabel }}
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded text-sm">
                                {{ $statusLabel }}
                            </span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIWAYAT STOK --}}
            <div class="mt-12">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">
                    Riwayat Pemasukan & Pengeluaran Stok
                </h3>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table id="riwayatStokTable" class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Batch</th>
                                <th class="px-4 py-2 border">Jenis</th>
                                <th class="px-4 py-2 border">Quantity</th>
                                <th class="px-4 py-2 border">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatStok ?? [] as $row)
                                <tr class="text-center">
                                    <td class="px-4 py-2 border">
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $row['batch_number'] }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        @if ($row['type'] === 'masuk')
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                                Masuk
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                                Keluar
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border font-semibold">
                                        {{ $row['type'] === 'masuk' ? '+' : '-' }}{{ $row['quantity'] }}
                                    </td>
                                    <td class="px-4 py-2 border">
                                        {{ $row['keterangan'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-2 border text-center text-gray-500">
                                        Belum ada riwayat stok
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden w-full" id="mobileCardWrapper">

                    {{-- TOP --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="mx-1 border-gray-300 rounded-md text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            entries
                        </div>
                    </div>

                    {{-- CARDS --}}
                    <div id="mobileCards" class="space-y-3">
                        @forelse ($riwayatStok ?? [] as $row)
                        <div class="mobile-card bg-white border rounded-lg shadow-sm">
                            
                            <div class="px-4 py-3 bg-gray-50 border-b space-y-2">
                                <div class="flex justify-between items-center">
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d-m-Y') }}
                                    </div>
                                    @if ($row['type'] === 'masuk')
                                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                            Masuk
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                            Keluar
                                        </span>
                                    @endif
                                </div>

                                <h4 class="font-semibold text-gray-900">
                                    Batch: {{ $row['batch_number'] }}
                                </h4>
                            </div>

                            <div class="px-4 py-3 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Quantity:</span>
                                    <span class="font-semibold">
                                        {{ $row['type'] === 'masuk' ? '+' : '-' }}{{ $row['quantity'] }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Keterangan:</span>
                                    <span>{{ $row['keterangan'] }}</span>
                                </div>
                            </div>

                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            Belum ada riwayat stok
                        </div>
                        @endforelse
                    </div>

                    {{-- INFO + PAGINATION --}}
                    @if(count($riwayatStok ?? []) > 0)
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
                    </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let dataTableInstance = null;

    const cards = Array.from(document.querySelectorAll('.mobile-card'));
    const pagination = document.getElementById('mobilePagination');
    const info = document.getElementById('mobileInfo');
    const perPageSelect = document.getElementById('mobilePerPage');

    // Skip if no cards
    if (cards.length === 0) return;

    let perPage = parseInt(perPageSelect.value);
    let currentPage = 1;

    function renderMobile() {
        const total = cards.length;
        const totalPages = Math.ceil(total / perPage);
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        cards.forEach((card, i) => {
            card.style.display = i >= start && i < end ? 'block' : 'none';
        });

        info.textContent = `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        pagination.innerHTML = '';
        const maxVisible = 5;

        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        const btn = (label, disabled, active, cb) => {
            const b = document.createElement('button');
            b.textContent = label;
            b.disabled = disabled;
            b.className = `px-3 py-1 text-sm rounded border
                ${active ? 'bg-blue-600 text-white' : 'bg-white'}
                ${disabled ? 'opacity-50' : ''}`;
            b.onclick = cb;
            return b;
        };

        pagination.appendChild(btn('Prev', currentPage === 1, false, () => {
            currentPage--; renderMobile();
        }));

        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(btn(i, false, i === currentPage, () => {
                currentPage = i; renderMobile();
            }));
        }

        pagination.appendChild(btn('Next', currentPage === totalPages, false, () => {
            currentPage++; renderMobile();
        }));
    }

    perPageSelect.addEventListener('change', () => {
        perPage = parseInt(perPageSelect.value);
        currentPage = 1;
        renderMobile();
    });

    function handleResponsive() {
        if (window.innerWidth >= 1024) {
            if (!dataTableInstance) {
                dataTableInstance = new DataTable('#riwayatStokTable', {
                    perPage: 10,
                    perPageSelect: [10, 25, 50],
                    searchable: true,
                    sortable: true,
                    labels: {
                        placeholder: "Cari...",
                        perPage: "data / halaman",
                        noRows: "Belum ada riwayat stok",
                        info: "Menampilkan {start} - {end} dari {rows} data",
                    }
                });
            }
        } else {
            if (dataTableInstance) {
                dataTableInstance.destroy();
                dataTableInstance = null;
            }
            renderMobile();
        }
    }

    handleResponsive();
    window.addEventListener('resize', handleResponsive);
});
</script>
@endpush