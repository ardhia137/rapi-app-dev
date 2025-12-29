@extends('layouts.app')

@section('title', __('Tambah Batch Produk'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Tambah Product Batches') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                    <h3 class="mb-4">{{ __('Tambah Batch Produk') }}</h3>

                    <form method="POST" action="{{ route('product-batches.store') }}">
                        @csrf

                        {{-- barcode --}}
                        <div class="mb-4">
                            <label for="barcode" class="block text-sm font-medium text-gray-700">Barcode Produk</label>

                            <input type="text" name="barcode" id="barcode" value="{{ request('barcode') }}"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                    focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                placeholder="Masukkan Barcode Produk">
                        </div>

                        {{-- Produk --}}
                        <div class="mb-4">
                            <label for="produk" class="block text-sm font-medium text-gray-700">
                                Produk <span class="text-red-500">*</span>
                            </label>

                            <select name="produk" id="produk"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                required>

                                <option value="" disabled selected>Pilih Produk</option>

                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" data-barcode="{{ $p->barcode }}"
                                        data-nama="{{ $p->nama_produk }}">
                                        {{ $p->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih produk terlebih dahulu untuk generate batch number
                            </p>
                        </div>

                        {{-- Kode Batch --}}
                        <div class="mb-4">
                            <label for="batch_number" class="block text-sm font-medium text-gray-700">
                                Batch Number <span class="text-red-500">*</span>
                            </label>

                            <div class="flex gap-2">
                                <input type="text" name="batch_number" id="batch_number" required readonly
                                    placeholder="Klik Generate"
                                    class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">

                                <button type="button" onclick="generateBatchCode()"
                                    class="mt-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 whitespace-nowrap">
                                    Generate
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: PB-DDMMYY-[Huruf][Jam] (contoh: PB-261225-K1622)
                            </p>
                        </div>


                        {{-- Harga Beli --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Harga Beli <span
                                    class="text-red-500">*</span></label>

                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>

                                <input type="text" id="harga_display"
                                    class="mt-1 block w-full px-3 py-2 pl-10 border rounded-md shadow-sm
                                        focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    placeholder="0">


                                <input type="hidden" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}"
                                    required>
                            </div>

                            @error('harga_beli')
                                <p class="text-red-600 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Masuk --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_masuk" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Tanggal Expired --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tanggal Expired <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_expired" id="tanggal_expired" disabled required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                        </div>

                        {{-- Qty Masuk --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Kuantitas Masuk <span
                                    class="text-red-500">*</span></label>
                            <input type="number" id="qty_masuk" name="quantity_masuk" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Qty Sekarang (readonly) --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Kuantitas Sekarang</label>
                            <input type="number" id="qty_sekarang" name="quantity_sekarang" readonly
                                class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Supplier --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Supplier</label>
                            <input type="text" name="supplier"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Status <span
                                    class="text-red-500">*</span></label>
                            <select name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" disabled selected>Pilih Status</option>
                                <option value="active">Aktif</option>
                                <option value="expired">Kadaluwarsa</option>
                                <option value="sold_out">Habis Terjual</option>
                            </select>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Simpan
                        </button>

                        <a href="{{ route('product-batches.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Batal
                        </a>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // === UTILITAS FORMAT RUPIAH ===
        function formatRupiah(angka) {
            return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRupiah(angka) {
            return angka.replace(/[^0-9]/g, '');
        }

        // === HARGA – Format Rupiah ===
        const hargaDisplay = document.getElementById('harga_display');
        const hargaHidden = document.getElementById('harga_beli');

        // Jika ada old value → tampilkan terformat
        if (hargaHidden.value) {
            hargaDisplay.value = formatRupiah(hargaHidden.value);
        }

        hargaDisplay.addEventListener('input', function(e) {
            let value = unformatRupiah(e.target.value);
            hargaHidden.value = value;

            if (value) {
                e.target.value = formatRupiah(value);
            } else {
                e.target.value = '';
            }
        });

        // Validasi Harga sebelum submit
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!hargaHidden.value) {
                e.preventDefault();
                alert('Harga beli harus diisi');
            }
        });

        function generateBatchCode() {
            const produkSelect = document.getElementById('produk');
            const selectedOption = produkSelect.options[produkSelect.selectedIndex];

            // Validasi: Produk harus dipilih
            if (!selectedOption || !selectedOption.value) {
                alert('⚠️ Pilih produk terlebih dahulu!');
                return;
            }

            // Ambil nama produk dari data-nama
            const namaProduk = selectedOption.dataset.nama || selectedOption.text;

            // Ambil huruf pertama dari nama produk (uppercase)
            const hurufPertama = namaProduk.charAt(0).toUpperCase();

            // Get current date
            const now = new Date();

            // Format: DDMMYY
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = String(now.getFullYear()).slice(-2); // Ambil 2 digit terakhir
            const tanggal = day + month + year;

            // Format: HHMM (jam dan menit)
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const jam = hours + minutes;

            // Format Final: PB-DDMMYY-[Huruf][Jam]
            const batchCode = `PB-${tanggal}-${hurufPertama}${jam}`;

            // Set ke input
            document.getElementById('batch_number').value = batchCode;

            console.log('Batch Code Generated:', batchCode);
        }

        // Auto-sync Qty Masuk → Qty Sekarang
        document.getElementById('qty_masuk').addEventListener('input', function() {
            document.getElementById('qty_sekarang').value = this.value;
        });

        // Tanggal Expired Logic
        const tanggalMasuk = document.querySelector('input[name="tanggal_masuk"]');
        const tanggalExpired = document.getElementById('tanggal_expired');

        tanggalExpired.disabled = true;

        tanggalMasuk.addEventListener('change', function() {
            const masuk = this.value;

            if (masuk) {
                tanggalExpired.disabled = false;
                tanggalExpired.min = masuk;

                if (tanggalExpired.value && tanggalExpired.value < masuk) {
                    tanggalExpired.value = '';
                }
            } else {
                tanggalExpired.disabled = true;
                tanggalExpired.value = '';
            }
        });

        // Barcode Input Logic
        const barcodeInput = document.getElementById('barcode');
        const produkSelect = document.getElementById('produk');

        // Prefill selection if barcode comes from query
        document.addEventListener('DOMContentLoaded', function(){
            const bc = (barcodeInput?.value || '').trim();
            if (bc) {
                let found = false;
                for (let option of produkSelect.options) {
                    if (option.dataset.barcode === bc) {
                        option.selected = true;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    // keep barcode, product stays unselected
                }
            }
        });

        // ===== 1. BARCODE → PRODUK =====
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const barcode = this.value.trim();
                let found = false;

                for (let option of produkSelect.options) {
                    if (option.dataset.barcode === barcode) {
                        option.selected = true;
                        found = true;
                        break;
                    }
                }

                if (!found && barcode !== '') {
                    alert('Produk dengan barcode tersebut tidak ditemukan');
                    produkSelect.selectedIndex = 0;
                }
            }
        });

        // ===== 2. PRODUK → BARCODE =====
        produkSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const barcode = selected.dataset.barcode || '';

            barcodeInput.value = barcode;
        });
    </script>
@endpush
