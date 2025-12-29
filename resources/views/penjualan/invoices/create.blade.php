@extends('layouts.app')

@section('title', __('Tambah Penjualan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Tambah Penjualan') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Tambah Penjualan') }}</h3>
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invoices.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Pelanggan
                            </label>

                            <div class="flex gap-4 mb-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="customer_type" value="existing" checked
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">Pelanggan Terdaftar</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="customer_type" value="new"
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">Pelanggan Baru</span>
                                </label>
                            </div>

                            <div id="existing-customer" class="mb-4">
                                <label for="customer_id" class="block text-sm font-medium text-gray-700">
                                    Pilih Pelanggan
                                </label>
                                <select name="customer_id" id="customer_id"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option value="" disabled selected>Pilih Pelanggan</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->nama_customer }} - {{ $customer->kategori_pelanggan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="new-customer" class="mb-4 hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700">
                                        Nama Pelanggan Baru
                                    </label>
                                    <input type="text" name="customer_name" id="customer_name"
                                        placeholder="Masukkan nama pelanggan..."
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="kategori_pelanggan"
                                        class="block text-sm font-medium text-gray-700">{{ __('Kategori Pelanggan') }}</label>
                                    <select name="kategori_pelanggan" id="kategori_pelanggan"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                        required>
                                        <option value="Toko">{{ __('Toko') }}</option>
                                        <option value="Konsumen" selected>{{ __('Konsumen') }}</option>
                                        <option value="Aplikator/Tukang">{{ __('Aplikator/Tukang') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_invoice"
                                    class="block text-sm font-medium text-gray-700">{{ __('Tanggal Invoice') }}</label>
                                <input type="date" name="tanggal_invoice" id="tanggal_invoice"
                                    value="{{ old('tanggal_invoice') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    required>
                            </div>
                            <div>
                                <label for="tanggal_jatuh_tempo"
                                    class="block text-sm font-medium text-gray-700">{{ __('Jatuh Tempo') }}</label>
                                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo"
                                    value="{{ old('tanggal_jatuh_tempo') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="invoice_number"
                                class="block text-sm font-medium text-gray-700">{{ __('Nomor Invoice') }}</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" name="invoice_number" id="invoice_number"
                                    value="{{ old('invoice_number') }}"
                                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    readonly placeholder="Klik Generate">
                                <button type="button" id="generate-invoice"
                                    class="px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">{{ __('Generate') }}</button>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold mb-2">{{ __('Produk') }}</h4>
                            <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label for="scan-barcode" class="block text-xs text-gray-600">Barcode Produk</label>
                                    <input type="text" id="scan-barcode"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                        placeholder="Masukkan Barcode" autocomplete="off">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="scan-clear"
                                        class="px-3 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Bersihkan</button>
                                </div>
                            </div>
                            <div id="items-wrapper" class="space-y-3">
                                <div class="item-row grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Produk') }}</label>
                                        <select name="items[0][product_id]"
                                            class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                            required>
                                            <option value="" disabled selected>{{ __('Pilih Produk') }}</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-barcode="{{ $product->barcode }}"
                                                    data-price="{{ $product->harga ?? ($product->price ?? 0) }}">
                                                    {{ $product->nama_produk ?? ($product->nama ?? 'Produk #' . $product->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                                        <select name="items[0][batch_id]"
                                            class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                            required>
                                            <option value="" disabled selected>{{ __('Pilih Batch') }}</option>
                                            @foreach ($batches as $batch)
                                                <option value="{{ $batch->id }}"
                                                    data-product="{{ $batch->product_id }}"
                                                    data-stock="{{ (int) $batch->quantity_sekarang }}">
                                                    {{ $batch->batch_number }} —
                                                    {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->translatedFormat('F') }}
                                                    — Stok: {{ $batch->quantity_sekarang }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Qty') }}</label>
                                        <input type="number" name="items[0][quantity]"
                                            class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Harga') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text"
                                                class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                                placeholder="0" autocomplete="off" />
                                            <input type="hidden" name="items[0][harga]" class="item-price" required />
                                            <button type="button"
                                                class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                                                aria-label="Hapus item">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-item"
                                class="mt-3 px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">{{ __('Tambah Produk') }}</button>
                        </div>
                        <div class="mb-6">
                            <div class="space-y-3">
                                <div class="item-row grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Ongkos Kirim') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text" id="ongkos-kirim-display"
                                                class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                                placeholder="0" autocomplete="off" />
                                            <input type="hidden" name="ongkos_kirim" id="ongkos-kirim" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Diskon (Opsional)') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text" id="diskon-display"
                                                class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                                placeholder="0" autocomplete="off" />
                                            <input type="hidden" name="diskon" id="diskon" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="metode_pembayaran"
                                class="block text-sm font-medium text-gray-700">{{ __('Metode Pembayaran') }}</label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="tunai"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio">
                                <span class="text-sm text-gray-700">Tunai (Cash)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="transfer"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio">
                                <span class="text-sm text-gray-700">Transfer (TF)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="qris"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio">
                                <span class="text-sm text-gray-700">QRIS</span>
                            </label>
                        </div>
                        <div class="mb-4" id="bukti-pembayaran-wrapper" style="display:none;">
                            <label for="bukti_setor" class="block text-sm font-medium text-gray-700">
                                {{ __('Bukti Pembayaran') }}
                                <span class="text-xs text-gray-500">(dari Customer)</span>
                            </label>
                            <input type="file" name="bukti_setor" id="bukti_setor" accept="image/*"
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB). Bisa diisi untuk semua
                                metode pembayaran termasuk Tunai.</p>

                            <!-- Preview Image -->
                            <div class="mt-2 hidden" id="preview-bukti-pembayaran">
                                <p class="text-xs text-gray-600 mb-1">Preview:</p>
                                <div class="relative inline-block">
                                    <img id="preview-img-pembayaran" src="" alt="Preview"
                                        class="h-32 rounded border">
                                    <button type="button" onclick="cancelPreviewPembayaran()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="status_pembayaran"
                                class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
                            <select name="status_pembayaran" id="status_pembayaran"
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="unpaid" @selected(old('status_pembayaran', 'unpaid') == 'unpaid')>Belum Lunas</option>
                                <option value="paid" @selected(old('status_pembayaran') == 'paid')>Lunas</option>
                                <option value="overdue" @selected(old('status_pembayaran') == 'overdue')>Terlambat</option>
                                <option value="cancelled" @selected(old('status_pembayaran') == 'cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <div class="text-right">
                                <span class="text-sm text-gray-600">{{ __('Grand Total') }}</span>
                                <span id="grand-total" class="text-lg font-semibold">0</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
                            <a href="{{ route('invoices.index') }}"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            let index = 1;
            const wrapper = document.getElementById('items-wrapper');
            const addBtn = document.getElementById('add-item');
            const grandEl = document.getElementById('grand-total');
            const scanInput = document.getElementById('scan-barcode');
            const scanClear = document.getElementById('scan-clear');
            const initialBarcode = @json(request('barcode'));

            const radios = document.querySelectorAll('input[name="customer_type"]');
            const existing = document.getElementById('existing-customer');
            const newer = document.getElementById('new-customer');

            const PRODUCT_CATALOG = {!! json_encode(
                $products->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'barcode' => $p->barcode,
                            'price' => $p->harga ?? ($p->price ?? 0),
                        ];
                    })->values()->toArray(),
            ) !!};

            radios.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.value === 'existing') {
                        existing.classList.remove('hidden');
                        newer.classList.add('hidden');
                    } else {
                        existing.classList.add('hidden');
                        newer.classList.remove('hidden');
                    }
                });
            });

            function formatRupiah(num) {
                // Pastikan input string/number, hilangkan .00 di belakang jika ada
                num = num.toString();
                // Jika ada koma/desimal, buang bagian desimal
                if (num.indexOf('.') !== -1) {
                    num = num.split('.')[0];
                }
                if (num.indexOf(',') !== -1) {
                    num = num.split(',')[0];
                }
                // Format ribuan
                return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function unformatRupiah(str) {
                return (str || '').toString().replace(/[^0-9]/g, '');
            }

            function recalc() {
                let total = 0;
                // Hitung total produk
                wrapper.querySelectorAll('.item-row').forEach(row => {
                    const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value || 0);
                    const hargaRawEl = row.querySelector('.item-price');
                    const harga = parseFloat(hargaRawEl?.value || 0);
                    total += (qty * harga);
                });
                // Tambahkan ongkos kirim
                const ongkirVal = document.getElementById('ongkos-kirim')?.value;
                const ongkir = ongkirVal ? parseInt(ongkirVal, 10) : 0;
                if (!isNaN(ongkir) && ongkirVal !== '') total += ongkir;
                // Kurangi diskon
                const diskonVal = document.getElementById('diskon')?.value;
                const diskon = diskonVal ? parseInt(diskonVal, 10) : 0;
                if (!isNaN(diskon) && diskonVal !== '') total -= diskon;
                if (total < 0) total = 0;
                grandEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }
            // Format rupiah untuk ongkos kirim dan diskon
            function handleRupiahInput(displayId, hiddenId) {
                const display = document.getElementById(displayId);
                const hidden = document.getElementById(hiddenId);
                if (!display || !hidden) return;
                display.addEventListener('input', function() {
                    let val = unformatRupiah(display.value);
                    // Jika kosong, biarkan kosong
                    if (val === '') {
                        display.value = '';
                        hidden.value = '';
                        recalc();
                        return;
                    }
                    display.value = formatRupiah(val);
                    hidden.value = val;
                    recalc();
                });
                // Inisialisasi jika ada value lama
                let val = unformatRupiah(display.value);
                if (val !== '') {
                    display.value = formatRupiah(val);
                    hidden.value = val;
                } else {
                    display.value = '';
                    hidden.value = '';
                }
            }

            handleRupiahInput('ongkos-kirim-display', 'ongkos-kirim');
            handleRupiahInput('diskon-display', 'diskon');

            function selectProductInRow(row, productId) {
                const sel = row.querySelector('.item-product');
                if (!sel) return;
                sel.value = String(productId);
                sel.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
                const qtyInput = row.querySelector('input[name$="[quantity]"]');
                if (qtyInput && (!qtyInput.value || qtyInput.value === '0')) qtyInput.value = 1;
            }

            function getEmptyRowOrAdd() {
                let row = Array.from(wrapper.querySelectorAll('.item-row')).find(r => {
                    const sel = r.querySelector('.item-product');
                    return sel && !sel.value;
                });
                if (!row) {
                    addBtn.click();
                    const rows = wrapper.querySelectorAll('.item-row');
                    row = rows[rows.length - 1];
                }
                return row;
            }

            function handleScan(code) {
                const bc = String(code || '').trim();
                if (!bc) return;
                const found = PRODUCT_CATALOG.find(p => (p.barcode || '') === bc);
                if (!found) {
                    alert('Produk dengan barcode tersebut tidak ditemukan');
                    return;
                }
                const row = getEmptyRowOrAdd();
                selectProductInRow(row, found.id);
                recalc();
            }

            addBtn.addEventListener('click', function() {
                const tpl = document.createElement('div');
                tpl.className = 'item-row grid grid-cols-1 sm:grid-cols-4 gap-3';
                tpl.innerHTML = `
                <div>
                    <label class="block text-xs text-gray-600">{{ __('Produk') }}</label>
                    <select name="items[${index}][product_id]" class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>{{ __('Pilih Produk') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-barcode="{{ $product->barcode }}" data-price="{{ $product->harga ?? ($product->price ?? 0) }}">{{ $product->nama_produk ?? ($product->nama ?? 'Produk #' . $product->id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                    <select name="items[${index}][batch_id]" class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>{{ __('Pilih Batch') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}" data-stock="{{ (int) $batch->quantity_sekarang }}">{{ $batch->batch_number }} — {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->translatedFormat('F') }} — Stok: {{ $batch->quantity_sekarang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Qty</label>
                    <input type="number" name="items[${index}][quantity]" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Harga</label>
                    <div class="mt-1 flex items-center">
                        <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                        <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />
                        <input type="hidden" name="items[${index}][harga]" class="item-price" required />
                        <button type="button" class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700" aria-label="Hapus item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            `;
                wrapper.appendChild(tpl);
                index++;
            });

            // Generate invoice number on button click
            document.getElementById('generate-invoice').addEventListener('click', function() {
                const now = new Date();
                const yy = String(now.getFullYear()).slice(-2);
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const datePart = yy + mm + dd;

                // Generate random 4 huruf + 1 angka
                const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                const numbers = '0123456789';

                let randomPart = '';
                // 4 huruf
                for (let i = 0; i < 4; i++) {
                    randomPart += letters[Math.floor(Math.random() * letters.length)];
                }
                // 1 angka
                randomPart += numbers[Math.floor(Math.random() * numbers.length)];

                // Format: INV-YYMMDD-XXXX#
                const invoiceNumber = `INV-${datePart}-${randomPart}`;
                document.getElementById('invoice_number').value = invoiceNumber;

                // Show success toast
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Nomor invoice berhasil digenerate',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
            // Auto-fill harga when product selected
            wrapper.addEventListener('change', function(e) {
                if (e.target.matches('select[name^="items"][name$="[product_id]"]')) {
                    const priceAttr = e.target.options[e.target.selectedIndex]?.getAttribute('data-price');
                    const row = e.target.closest('.item-row');
                    const priceDisplay = row.querySelector('.item-price-display');
                    const priceHidden = row.querySelector('.item-price');
                    if (priceDisplay && priceHidden && priceAttr) {
                        priceHidden.value = String(priceAttr);
                        priceDisplay.value = formatRupiah(String(priceAttr));
                        recalc();
                    }
                    // Filter batch options matching selected product
                    const productId = e.target.value;
                    const batchSelect = row.querySelector('.item-batch');
                    if (batchSelect) {
                        Array.from(batchSelect.options).forEach(opt => {
                            if (!opt.value) return; // skip placeholder
                            const p = opt.getAttribute('data-product');
                            opt.hidden = (p !== productId);
                        });
                        // Reset selection
                        batchSelect.value = '';
                        // Also reset qty max since batch changed
                        const qtyInput = row.querySelector('input[name$="[quantity]"]');
                        if (qtyInput) {
                            qtyInput.removeAttribute('max');
                        }
                    }
                }
                // When batch selected, set qty max from data-stock
                if (e.target.matches('select[name^="items"][name$="[batch_id]"]')) {
                    const row = e.target.closest('.item-row');
                    const qtyInput = row.querySelector('input[name$="[quantity]"]');
                    const opt = e.target.options[e.target.selectedIndex];
                    const stock = parseInt(opt?.getAttribute('data-stock') || '0', 10);
                    if (qtyInput) {
                        if (stock > 0) {
                            qtyInput.setAttribute('max', String(stock));
                            // Clamp current value to max
                            const cur = parseInt(qtyInput.value || '0', 10);
                            if (cur > stock) qtyInput.value = String(stock);
                        } else {
                            qtyInput.setAttribute('max', '0');
                            qtyInput.value = '0';
                        }
                    }
                }
            });

            wrapper.addEventListener('input', function(e) {
                if (e.target.classList.contains('item-price-display')) {
                    const row = e.target.closest('.item-row');
                    const priceHidden = row.querySelector('.item-price');
                    let val = unformatRupiah(e.target.value);
                    if (val === '') {
                        e.target.value = '';
                        priceHidden.value = '';
                        recalc();
                        return;
                    }
                    e.target.value = formatRupiah(val);
                    priceHidden.value = val;
                    recalc();
                }
            });

            // Recalculate on qty or harga change
            wrapper.addEventListener('input', function(e) {
                if (e.target.matches('input[name$="[quantity]"]')) {
                    const maxAttr = e.target.getAttribute('max');
                    const maxVal = maxAttr ? parseInt(maxAttr, 10) : null;

                    // Kalau kosong, biarin dulu
                    if (e.target.value === '') {
                        recalc();
                        return;
                    }

                    let val = parseInt(e.target.value, 10);

                    if (!isNaN(val) && maxVal !== null && val > maxVal) {
                        e.target.value = String(maxVal);
                    }

                    recalc();
                }
            });


            // Remove item row on click "x / Hapus"
            wrapper.addEventListener('click', function(e) {
                const btn = e.target.closest('.remove-item');
                if (!btn) return;
                const row = btn.closest('.item-row');
                if (row) {
                    row.remove();
                    recalc();
                }
            });

            // Scan handler: auto-handle without pressing Enter
            if (scanInput) {
                let scanTimer = null;
                const triggerScan = () => {
                    const val = (scanInput.value || '').trim();
                    if (!val) return;
                    handleScan(val);
                    scanInput.value = '';
                };
                scanInput.addEventListener('input', function() {
                    if (scanTimer) clearTimeout(scanTimer);
                    // small debounce to let scanner finish
                    scanTimer = setTimeout(triggerScan, 150);
                });
                // also trigger on change for scanners that fire change
                scanInput.addEventListener('change', triggerScan);
            }
            if (scanClear) {
                scanClear.addEventListener('click', function() {
                    scanInput && (scanInput.value = '');
                    scanInput && scanInput.focus();
                });
            }

            // Handle file input for bukti pembayaran
            const buktiFileInput = document.getElementById('bukti_setor');
            if (buktiFileInput) {
                buktiFileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Tidak Valid',
                            text: 'Harap pilih file gambar (JPG, PNG, JPEG).',
                            confirmButtonColor: '#2563eb'
                        });
                        this.value = '';
                        return;
                    }

                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: 'Ukuran file maksimal 2MB.',
                            confirmButtonColor: '#2563eb'
                        });
                        this.value = '';
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview-img-pembayaran').src = e.target.result;
                        document.getElementById('preview-bukti-pembayaran').classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);

                    // AUTO-SELECT STATUS PEMBAYARAN = 'PAID' (LUNAS)
                    const statusPembayaranSelect = document.getElementById('status_pembayaran');
                    if (statusPembayaranSelect) {
                        statusPembayaranSelect.value = 'paid';
                    }

                    // Show success toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Bukti pembayaran berhasil dipilih',
                        text: 'Status pembayaran otomatis diset "Lunas"',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
            }

            // Show error messages with SweetAlert if exists
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: `
                        <div class="text-left">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    `,
                    confirmButtonColor: '#2563eb'
                });
            @endif


            // Function to toggle bukti pembayaran visibility based on payment method
            function toggleBuktiPembayaran() {
                const radios = document.querySelectorAll('.metode-pembayaran-radio');
                let selected = null;
                radios.forEach(r => { if (r.checked) selected = r.value; });
                const wrapper = document.getElementById('bukti-pembayaran-wrapper');

                if (selected && selected !== 'tunai') {
                    // Show form for transfer/qris
                    wrapper.style.display = '';

                    // Show toast notification
                    if (selected === 'transfer') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Metode Transfer dipilih',
                            text: 'Jika ada bukti dari customer, upload untuk auto-set status setor "Sudah"',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                    } else if (selected === 'qris') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Metode QRIS dipilih',
                            text: 'Jika ada bukti dari customer, upload untuk auto-set status setor "Sudah"',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                    }
                } else {
                    // Hide form for tunai
                    wrapper.style.display = 'none';
                    // Clear file input
                    const fileInput = document.getElementById('bukti_setor');
                    if (fileInput) fileInput.value = '';
                    // Hide preview if shown
                    const preview = document.getElementById('preview-bukti-pembayaran');
                    if (preview) preview.classList.add('hidden');
                }
            }

            // Add event listeners to all metode pembayaran radio buttons
            document.querySelectorAll('.metode-pembayaran-radio').forEach(radio => {
                radio.addEventListener('change', toggleBuktiPembayaran);
            });

            // Run on initial load
            toggleBuktiPembayaran();

            // Also recalc on initial load (in case defaults present)
            recalc();

            // If barcode is passed via query, auto-add the product row
            if (initialBarcode) {
                handleScan(initialBarcode);
            }
        })();

        // Function to cancel preview (outside closure so onclick can access it)
        function cancelPreviewPembayaran() {
            document.getElementById('bukti_setor').value = '';
            document.getElementById('preview-bukti-pembayaran').classList.add('hidden');

            // RESET STATUS PEMBAYARAN KE 'UNPAID'
            const statusPembayaranSelect = document.getElementById('status_pembayaran');
            if (statusPembayaranSelect) {
                statusPembayaranSelect.value = 'unpaid';
            }

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Bukti pembayaran dibatalkan',
                text: 'Status pembayaran direset ke "Belum Lunas"',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }
    </script>
@endpush
