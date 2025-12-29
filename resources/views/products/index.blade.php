@extends('layouts.app')

@section('title', __('Produk'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Produk') }}</h2>
@endsection

@section('content')
    <div class="py-2 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg w-full">
                <div class="p-4 sm:p-6 lg:p-8">

                    {{-- HEADER --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-6 gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">Produk</h3>

                        <a href="{{ route('products.create') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            + Tambah Produk
                        </a>
                    </div>

                    {{-- ================= DESKTOP TABLE ================= --}}
                    <div class="hidden lg:block w-full overflow-x-auto">
                        <table id="dataTables" class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-center text-xs font-medium uppercase border">No</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium uppercase border">Barcode</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase border">Nama</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium uppercase border">Kategori</th>
                                    <th class="px-3 py-3 text-right text-xs font-medium uppercase border">Harga Jual</th>
                                    <th class="px-3 py-3 text-right text-xs font-medium uppercase border">Harga Beli</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium uppercase border">Stok</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium uppercase border">Status</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium uppercase border">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($products as $p)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-center border">{{ $loop->iteration }}</td>

                                        <td class="px-3 py-2 border">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="flex justify-center w-full">
                                                    {!! DNS1D::getBarcodeHTML($p->barcode, 'C128', 1.5, 40) !!}
                                                </div>

                                                <div class="text-xs font-semibold mt-1 text-center">
                                                    {{ $p->barcode }}
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-3 py-2 border font-medium">{{ $p->nama_produk }}</td>
                                        <td class="px-3 py-2 border">{{ $p->kategori }}</td>

                                        <td class="px-3 py-2 border text-right">
                                            Rp {{ number_format($p->harga, 0, ',', '.') }}
                                        </td>

                                        <td class="px-3 py-2 border text-right">
                                            @if ($p->latestBatch)
                                                Rp {{ number_format($p->latestBatch->harga_beli, 0, ',', '.') }}
                                            @else
                                                <span class="italic text-gray-400">Belum ada</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-2 border text-center">
                                            {{ $p->batches->sum('quantity_sekarang') }}
                                        </td>

                                        <td class="px-3 py-2 border text-center">
                                            @php
                                                $statusLabel =
                                                    $p->status === 'available' ? 'Tersedia' : 'Tidak Tersedia';
                                            @endphp

                                            @if ($p->batches->sum('quantity_sekarang') >= $p->min_stok_alert)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                                    {{ $statusLabel }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">
                                                    {{ $statusLabel }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-2 border">
                                            <div class="flex justify-center gap-2">
                                                <button
                                                    onclick="openBarcodeModal({{ $p->id }}, '{{ $p->nama_produk }}')"
                                                    class="text-green-600 hover:underline">
                                                    Unduh Barcode
                                                </button>
                                                <a href="{{ route('products.show', $p->id) }}"
                                                    class="text-indigo-600 hover:underline">Detail</a>
                                                <a href="{{ route('products.edit', $p->id) }}"
                                                    class="text-blue-600 hover:underline">Edit</a>
                                                <form action="{{ route('products.destroy', $p->id) }}" method="POST"
                                                    data-confirm-delete>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
                            @foreach ($products as $p)
                                <div class="mobile-card bg-white border rounded-lg shadow-sm">

                                    <div class="px-4 py-3 bg-gray-50 border-b space-y-2">

                                        <div class="text-xs text-gray-500">No. {{ $loop->iteration }}</div>

                                        {{-- BARCODE --}}
                                        <div class="flex flex-col items-center justify-center">
                                            {!! DNS1D::getBarcodeHTML($p->barcode, 'C128', 1.6, 50) !!}
                                            <div class="text-xs font-semibold mt-1">
                                                {{ $p->barcode }}
                                            </div>
                                        </div>

                                        <h4 class="font-semibold text-gray-900 text-center">
                                            {{ $p->nama_produk }}
                                        </h4>

                                        <div class="text-xs text-gray-600 text-center">
                                            {{ $p->kategori }}
                                        </div>
                                    </div>

                                    <div class="px-4 py-3 space-y-1 text-sm">
                                        <div>Harga Jual: <b>Rp {{ number_format($p->harga, 0, ',', '.') }}</b></div>
                                        <div>Harga Beli:
                                            @if ($p->latestBatch)
                                                Rp {{ number_format($p->latestBatch->harga_beli, 0, ',', '.') }}
                                            @else
                                                <span class="italic text-gray-400">Belum ada</span>
                                            @endif
                                        </div>
                                        <div>Stok: {{ $p->batches->sum('quantity_sekarang') }}</div>
                                        @php
                                            $statusLabel = $p->status === 'available' ? 'Tersedia' : 'Tidak Tersedia';
                                        @endphp
                                        <div>Status: @if ($p->batches->sum('quantity_sekarang') >= $p->min_stok_alert)
                                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">
                                                    {{ $statusLabel }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">
                                                    {{ $statusLabel }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="px-4 py-3 border-t flex gap-2">

                                        <button
                                            onclick="openBarcodeModal({{ $p->id }}, '{{ $p->nama_produk }}')"
                                            class="flex-1 min-h-[44px] inline-flex items-center justify-center
                                        px-3 py-2 border border-green-600 rounded text-green-600">
                                            Unduh
                                        </button>

                                        <a href="{{ route('products.show', $p->id) }}"
                                            class="flex-1 min-h-[44px] inline-flex items-center justify-center
                                        px-3 py-2 border border-indigo-600 rounded text-indigo-600">
                                            Detail
                                        </a>

                                        <a href="{{ route('products.edit', $p->id) }}"
                                            class="flex-1 min-h-[44px] inline-flex items-center justify-center
                                        px-3 py-2 border border-blue-600 rounded text-blue-600">
                                            Edit
                                        </a>

                                        <form action="{{ route('products.destroy', $p->id) }}" method="POST"
                                            class="flex-1"
                                            data-confirm-delete>
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="w-full min-h-[44px] inline-flex items-center justify-center
                                            px-3 py-2 border border-red-600 rounded text-red-600">
                                                Hapus
                                            </button>
                                        </form>

                                    </div>

                                </div>
                            @endforeach
                        </div>

                        {{-- INFO + PAGINATION --}}
                        <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                            <div id="mobileInfo" class="text-sm text-gray-600"></div>
                            <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UKURAN BARCODE --}}
    <div id="barcodeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Pengaturan Ukuran Barcode (MM)
                </h3>

                <form id="barcodeForm" method="GET">



                    {{-- HEIGHT --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tinggi (Height) - mm
                        </label>
                        <input type="number" id="barcodeHeight" name="height" value="32" min="13"
                            max="118" step="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-500 mt-1">
                            Contoh: 32mm
                        </p>
                    </div>
                    {{-- WIDTH --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Lebar (Width) - mm
                        </label>

                        <input type="number" id="barcodeWidth" name="width" value="64" min="13"
                            max="118" step="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">

                        <p class="text-xs text-gray-500 mt-1">
                            Contoh: 64mm
                        </p>
                    </div>

                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" onclick="closeBarcodeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Batal
                        </button>

                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Download
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let dataTableInstance = null;

            const cards = Array.from(document.querySelectorAll('.mobile-card'));
            const pagination = document.getElementById('mobilePagination');
            const info = document.getElementById('mobileInfo');
            const perPageSelect = document.getElementById('mobilePerPage');

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
                    currentPage--;
                    renderMobile();
                }));

                for (let i = startPage; i <= endPage; i++) {
                    pagination.appendChild(btn(i, false, i === currentPage, () => {
                        currentPage = i;
                        renderMobile();
                    }));
                }

                pagination.appendChild(btn('Next', currentPage === totalPages, false, () => {
                    currentPage++;
                    renderMobile();
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
                        dataTableInstance = new DataTable('#dataTables', {
                            responsive: true,
                            autoWidth: false
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

        // Modal Functions
        function openBarcodeModal(productId, productName) {
            document.getElementById('barcodeModal').classList.remove('hidden');
            const form = document.getElementById('barcodeForm');
            form.action = `/products/${productId}/barcode/download`;
        }

        function closeBarcodeModal() {
            document.getElementById('barcodeModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('barcodeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBarcodeModal();
            }
        });
    </script>
@endpush
