@extends('layouts.app')

@section('title', __('Edit Penjualan'))


@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Penjualan') }}</h2>
@endsection


@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Edit Penjualan') }}</h3>
                    @if ($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="mb-4">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">{{ __('Pelanggan') }}</label>
                            <select name="customer_id" id="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                <option value="" disabled selected>{{ __('Pilih Pelanggan') }}</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id', $invoice->customer_id) == $customer->id)>{{ $customer->nama_customer }} - {{ $customer->kategori_pelanggan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="tanggal_invoice" class="block text-sm font-medium text-gray-700">{{ __('Tanggal Invoice') }}</label>
                                <input type="date" name="tanggal_invoice" id="tanggal_invoice" value="{{ old('tanggal_invoice', $invoice->tanggal_invoice) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="tanggal_jatuh_tempo" class="block text-sm font-medium text-gray-700">{{ __('Jatuh Tempo') }}</label>
                                <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $invoice->tanggal_jatuh_tempo) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700">{{ __('Nomor Invoice') }}</label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" readonly>
                            </div>
                        </div>
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold mb-2">{{ __('Produk') }}</h4>
                            <div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs text-gray-600">Barcode Produk</label>
                                    <input type="text" id="scan-barcode"
                                        class="mt-1 w-full border-gray-300 rounded-md shadow-sm sm:text-sm"
                                        placeholder="Masukkan Barcode"
                                        autocomplete="off">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="scan-clear"
                                        class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                                        Bersihkan
                                    </button>
                                </div>
                            </div>
                            <div id="items-wrapper" class="space-y-3">
                                @php $oldItems = old('items', $invoice->items->map(function($it){ return [
                                    'product_id' => $it->product_id,
                                    'batch_id' => $it->batch_id ?? null,
                                    'quantity' => $it->quantity,
                                    'harga' => $it->harga,
                                ]; })->toArray()); @endphp
                                @foreach($oldItems as $i => $item)
                                    <div class="item-row grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <div>
                                            <label class="block text-xs text-gray-600">{{ __('Produk') }}</label>
                                            <select name="items[{{ $i }}][product_id]" class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                                <option value="" disabled>{{ __('Pilih Produk') }}</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->harga ?? $product->price ?? 0 }}" @selected(($item['product_id'] ?? null) == $product->id)>
                                                        {{ $product->nama_produk ?? $product->nama ?? 'Produk #'.$product->id }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                                            <select name="items[{{ $i }}][batch_id]" class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                                <option value="" disabled>{{ __('Pilih Batch') }}</option>
                                                @foreach($batches as $batch)
                                                    <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}" data-stock="{{ (int) $batch->quantity_sekarang }}" @selected(($item['batch_id'] ?? null) == $batch->id)>
                                                        {{ $batch->batch_number }} — {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->translatedFormat('F') }} — Stok: {{ $batch->quantity_sekarang }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">{{ __('Qty') }}</label>
                                            <input type="number" name="items[{{ $i }}][quantity]" min="1" value="{{ $item['quantity'] ?? 1 }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-600">{{ __('Harga') }}</label>
                                            <div class="mt-1 flex items-center">
                                                <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                                <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" value="{{ number_format($item['harga'] ?? 0, 0, ',', '.') }}" />
                                                <input type="hidden" name="items[{{ $i }}][harga]" value="{{ $item['harga'] ?? 0 }}" class="item-price" required>
                                                <button type="button" class="remove-item ml-2 px-2 py-2 bg-red-600 text-white rounded hover:bg-red-700" aria-label="Hapus item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" id="add-item" class="mt-3 px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700">{{ __('Tambah Produk') }}</button>
                        </div>
                        <div class="mb-6">
                            <div class="space-y-3">
                                <div class="item-row grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Ongkos Kirim') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text" id="ongkos-kirim-display" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" autocomplete="off" value="{{ old('ongkos_kirim', $invoice->ongkos_kirim ?? '') ? number_format(old('ongkos_kirim', $invoice->ongkos_kirim), 0, ',', '.') : '' }}" />
                                            <input type="hidden" name="ongkos_kirim" id="ongkos-kirim" value="{{ old('ongkos_kirim', $invoice->ongkos_kirim ?? '') }}"/>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">{{ __('Diskon (Opsional)') }}</label>
                                        <div class="mt-1 flex items-center">
                                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                                            <input type="text" id="diskon-display" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" autocomplete="off" value="{{ old('diskon', $invoice->diskon ?? '') ? number_format(old('diskon', $invoice->diskon), 0, ',', '.') : '' }}" />
                                            <input type="hidden" name="diskon" id="diskon" value="{{ old('diskon', $invoice->diskon ?? '') }}"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">{{ __('Metode Pembayaran') }}</label>
                            @php $metode = old('metode_pembayaran', $invoice->metode_pembayaran ?? null); @endphp
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="tunai" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio" @checked($metode === 'tunai')>
                                <span class="text-sm text-gray-700">Tunai (Cash)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="transfer" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio" @checked($metode === 'transfer')>
                                <span class="text-sm text-gray-700">Transfer (TF)</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="radio" name="metode_pembayaran" value="qris" class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 metode-pembayaran-radio" @checked($metode === 'qris')>
                                <span class="text-sm text-gray-700">QRIS</span>
                            </label>
                        </div>
                        <div class="mb-4" id="bukti-pembayaran-wrapper" style="display:{{ ($metode && $metode !== 'tunai') ? '' : 'none' }};">
                            <label for="bukti_setor" class="block text-sm font-medium text-gray-700">
                                {{ __('Bukti Pembayaran') }}
                                <span class="text-xs text-gray-500">(dari Customer)</span>
                            </label>
                            <input type="file" name="bukti_setor" id="bukti_setor" accept="image/*"
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB). Bisa diisi untuk semua metode pembayaran termasuk Tunai.</p>

                            @if($invoice->bukti_setor)
                                <div class="mt-2" id="current-bukti-pembayaran">
                                    <p class="text-xs text-gray-600 mb-1">Bukti pembayaran saat ini:</p>
                                    <div class="relative inline-block">
                                        <img src="{{ asset('storage/'.$invoice->bukti_setor) }}" alt="Bukti Pembayaran"
                                            class="h-32 rounded border cursor-pointer hover:opacity-80 transition"
                                            onclick="previewFullImage('{{ asset('storage/'.$invoice->bukti_setor) }}')">
                                        <div class="absolute top-1 right-1">
                                            <span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded">✓ Ada</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Preview New Image -->
                            <div class="mt-2 hidden" id="preview-bukti-pembayaran">
                                <p class="text-xs text-gray-600 mb-1">Preview gambar baru:</p>
                                <div class="relative inline-block">
                                    <img id="preview-img-pembayaran" src="" alt="Preview" class="h-32 rounded border">
                                    <button type="button" onclick="cancelPreviewPembayaranEdit()"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="status_pembayaran" class="block text-sm font-medium text-gray-700">{{ __('Status Pembayaran') }}</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="unpaid" @selected(old('status_pembayaran', $invoice->status_pembayaran)=='unpaid')>Belum Lunas</option>
                                <option value="paid" @selected(old('status_pembayaran', $invoice->status_pembayaran)=='paid')>Lunas</option>
                                <option value="overdue" @selected(old('status_pembayaran', $invoice->status_pembayaran)=='overdue')>Terlambat</option>
                                <option value="cancelled" @selected(old('status_pembayaran', $invoice->status_pembayaran)=='cancelled')>Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-4" id="alasan-cancel-wrapper" style="display:{{ old('status_pembayaran', $invoice->status_pembayaran) == 'cancelled' ? '' : 'none' }};">
                            <label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }} <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex gap-2">
                                <input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel', $invoice->alasan_cancel) }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="Masukkan alasan pembatalan...">
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="text-right">
                                <span class="text-sm text-gray-600">{{ __('Grand Total') }}</span>
                                <span id="grand-total" class="text-lg font-semibold">Rp {{ number_format(old('grand_total', $invoice->grand_total), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
                            <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
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
        let index = {{ count(old('items', $invoice->items)) }};
        const wrapper = document.getElementById('items-wrapper');
        const addBtn = document.getElementById('add-item');
        const grandEl = document.getElementById('grand-total');
        const statusPembayaranEl = document.getElementById('status_pembayaran');
        const alasanCancelEl = document.getElementById('alasan_cancel');

        function toggleAlasanCancelRequired() {
            const isCancelled = statusPembayaranEl && statusPembayaranEl.value === 'cancelled';
            const wrapper = document.getElementById('alasan-cancel-wrapper');

            if (isCancelled) {
                // Show wrapper dan set required
                if (wrapper) wrapper.style.display = '';
                if (alasanCancelEl) alasanCancelEl.setAttribute('required', 'required');
            } else {
                // Hide wrapper dan remove required
                if (wrapper) wrapper.style.display = 'none';
                if (alasanCancelEl) {
                    alasanCancelEl.removeAttribute('required');
                    alasanCancelEl.value = ''; // Clear value
                }
            }
        }

        const scanInput = document.getElementById('scan-barcode');
        const scanClear = document.getElementById('scan-clear');

        const PRODUCT_CATALOG = {!! json_encode(
            $products->map(fn($p) => [
                'id' => $p->id,
                'barcode' => $p->barcode,
                'price' => $p->harga ?? $p->price ?? 0,
            ])->values()
        ) !!};

        function handleScan(code) {
            const bc = String(code || '').trim();
            if (!bc) return;

            const found = PRODUCT_CATALOG.find(p => p.barcode === bc);
            if (!found) {
                alert('Produk tidak ditemukan');
                return;
            }

            const row = wrapper.querySelector('.item-row') || addBtn.click();
            const sel = row.querySelector('.item-product');
            sel.value = found.id;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        }

        if (scanInput) {
            let timer;
            scanInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    handleScan(scanInput.value);
                    scanInput.value = '';
                }, 150);
            });
        }

        if (scanClear) {
            scanClear.addEventListener('click', () => scanInput.value = '');
        }


        function formatRupiah(num) {
            num = num.toString();
            if (num.indexOf('.') !== -1) num = num.split('.')[0];
            if (num.indexOf(',') !== -1) num = num.split(',')[0];
            return num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRupiah(str) {
            return (str || '').toString().replace(/[^0-9]/g, '');
        }

        function recalc() {
            let total = 0;
            wrapper.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value || 0);
                const hargaRawEl = row.querySelector('.item-price');
                const harga = parseFloat(hargaRawEl?.value || 0);
                total += (qty * harga);
            });
            // Tambahkan ongkos kirim, kurangi diskon
            const ongkirVal = document.getElementById('ongkos-kirim')?.value;
            const diskonVal = document.getElementById('diskon')?.value;
            const ongkir = ongkirVal ? parseInt(ongkirVal, 10) : 0;
            const diskon = diskonVal ? parseInt(diskonVal, 10) : 0;
            if (!isNaN(ongkir) && ongkirVal !== '') total += ongkir;
            if (!isNaN(diskon) && diskonVal !== '') total -= diskon;
            if (total < 0) total = 0;
            grandEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        // Format rupiah untuk ongkir & diskon
        function handleRupiahInput(displayId, hiddenId) {
            const display = document.getElementById(displayId);
            const hidden = document.getElementById(hiddenId);
            if (!display || !hidden) return;
            display.addEventListener('input', function() {
                let val = (display.value || '').toString().replace(/[^0-9]/g, '');
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
            // init on load
            let val = (display.value || '').toString().replace(/[^0-9]/g, '');
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
                    const currentDiv = document.getElementById('current-bukti-pembayaran');
                    if (currentDiv) currentDiv.classList.add('opacity-50');
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
                    title: 'Bukti pembayaran baru dipilih',
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


        addBtn.addEventListener('click', function() {
            const tpl = document.createElement('div');
            tpl.className = 'item-row grid grid-cols-1 sm:grid-cols-4 gap-3';
            tpl.innerHTML = `
                <div>
                    <label class="block text-xs text-gray-600">Produk</label>
                    <select name="items[${index}][product_id]" class="item-product mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>Pilih Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->harga ?? $product->price ?? 0 }}">{{ $product->nama_produk ?? $product->nama ?? 'Produk #'.$product->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">{{ __('Batch') }}</label>
                    <select name="items[${index}][batch_id]" class="item-batch mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        <option value="" disabled selected>{{ __('Pilih Batch') }}</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" data-product="{{ $batch->product_id }}" data-stock="{{ (int) $batch->quantity_sekarang }}">{{ $batch->batch_number }} — {{ \Carbon\Carbon::parse($batch->tanggal_masuk)->translatedFormat('F') }} — Stok: {{ $batch->quantity_sekarang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Qty</label>
                    <input type="number" name="items[${index}][quantity]" min="1" value="1" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-600">Harga</label>
                    <div class="mt-1 flex items-center">
                        <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                        <input type="text" class="item-price-display w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />
                        <input type="hidden" name="items[${index}][harga]" class="item-price" required>
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

        // no invoice number generation on edit page

        // Auto-fill harga when product selected and filter batch options for the product
        wrapper.addEventListener('change', function(e){
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
                    // Reset qty max since batch changed
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
                        const cur = parseInt(qtyInput.value || '0', 10);
                        if (cur > stock) qtyInput.value = String(stock);
                    } else {
                        qtyInput.setAttribute('max', '0');
                        qtyInput.value = '0';
                    }
                }
            }
        });

        // Recalculate on qty or harga change (and clamp qty to max stock)
        wrapper.addEventListener('input', function(e){
            if (e.target.matches('input[name$="[quantity]"]')) {
                const maxAttr = e.target.getAttribute('max');
                const maxVal = maxAttr ? parseInt(maxAttr, 10) : null;

                if (e.target.value === '') {
                    recalc();
                    return;
                }

                let val = parseInt(e.target.value, 10);
                if (!isNaN(val) && maxVal !== null && val > maxVal) {
                    e.target.value = String(maxVal);
                }

                recalc();
                return;
            }
            if (e.target.classList.contains('item-price-display')) {
                const row = e.target.closest('.item-row');
                const hidden = row.querySelector('.item-price');
                const raw = unformatRupiah(e.target.value);
                hidden.value = raw;
                e.target.value = raw ? formatRupiah(raw) : '';
                recalc();
            }
        });

        // Apply initial qty max based on preselected batch in edit
        function applyInitialQtyMax() {
            wrapper.querySelectorAll('.item-row').forEach(row => {
                const batchSelect = row.querySelector('.item-batch');
                const qtyInput = row.querySelector('input[name$="[quantity]"]');
                if (!batchSelect || !qtyInput) return;
                const opt = batchSelect.options[batchSelect.selectedIndex];
                const stock = parseInt(opt?.getAttribute('data-stock') || '0', 10);
                if (stock > 0) {
                    qtyInput.setAttribute('max', String(stock));
                    const cur = parseInt(qtyInput.value || '0', 10);
                    if (cur > stock) qtyInput.value = String(stock);
                } else {
                    qtyInput.setAttribute('max', '0');
                    qtyInput.value = '0';
                }
            });
        }

        // Remove item row on click "x / Hapus"
        wrapper.addEventListener('click', function(e){
            const btn = e.target.closest('.remove-item');
            if (!btn) return;
            const row = btn.closest('.item-row');
            if (row) {
                row.remove();
                recalc();
            }
        });

        // Set initial max constraints and recalc on load
        applyInitialQtyMax();
        recalc();

        // Require alasan_cancel when status is cancelled
        if (statusPembayaranEl) {
            statusPembayaranEl.addEventListener('change', toggleAlasanCancelRequired);
            toggleAlasanCancelRequired();
        }

        // Function to toggle bukti pembayaran visibility based on payment method
        function toggleBuktiPembayaran() {
            const radios = document.querySelectorAll('.metode-pembayaran-radio');
            let selected = null;
            radios.forEach(r => { if (r.checked) selected = r.value; });
            const wrapper = document.getElementById('bukti-pembayaran-wrapper');

            if (selected && selected !== 'tunai') {
                // Show form for transfer/qris
                wrapper.style.display = '';
            } else {
                // Hide form for tunai
                wrapper.style.display = 'none';
                // Clear file input only if no existing bukti
                const hasExisting = {{ $invoice->bukti_setor ? 'true' : 'false' }};
                if (!hasExisting) {
                    const fileInput = document.getElementById('bukti_setor');
                    if (fileInput) fileInput.value = '';
                    const preview = document.getElementById('preview-bukti-pembayaran');
                    if (preview) preview.classList.add('hidden');
                }
            }
        }

        // Add event listeners to all metode pembayaran radio buttons
        document.querySelectorAll('.metode-pembayaran-radio').forEach(radio => {
            radio.addEventListener('change', toggleBuktiPembayaran);
        });

        // Run on initial load
        toggleBuktiPembayaran();
    })();

    // Helper functions (outside closure so onclick can access)
    function previewFullImage(imageUrl) {
        Swal.fire({
            imageUrl: imageUrl,
            imageAlt: 'Bukti Pembayaran',
            showConfirmButton: false,
            showCloseButton: true,
            width: 'auto',
            customClass: {
                image: 'max-h-96'
            }
        });
    }

    function cancelPreviewPembayaranEdit() {
        document.getElementById('bukti_setor').value = '';
        document.getElementById('preview-bukti-pembayaran').classList.add('hidden');
        const currentDiv = document.getElementById('current-bukti-pembayaran');
        if (currentDiv) currentDiv.classList.remove('opacity-50');

        // Cek apakah ada bukti lama
        const hasExistingBukti = {{ $invoice->bukti_setor ? 'true' : 'false' }};
        const statusPembayaranSelect = document.getElementById('status_pembayaran');

        if (!hasExistingBukti && statusPembayaranSelect) {
            // Jika TIDAK ada bukti lama, reset ke unpaid
            statusPembayaranSelect.value = 'unpaid';
        }
        // Jika ada bukti lama, status tetap paid (tidak reset)

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Bukti pembayaran baru dibatalkan',
            text: hasExistingBukti ? 'Bukti lama akan tetap digunakan' : 'Status pembayaran direset ke "Belum Lunas"',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }
</script>
@endpush
