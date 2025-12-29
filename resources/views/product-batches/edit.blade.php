@extends('layouts.app')

@section('title', __('Edit Batch Produk'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Product Batches') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">

                <h3 class="mb-4">{{ __('Edit Batch Produk') }}</h3>

                <form method="POST" action="{{ route('product-batches.update', $productBatch->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Barcode --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Barcode Produk</label>
                        <input type="text" name="barcode" id="barcode"
                            value="{{ old('barcode', $productBatch->product->barcode ?? '') }}"
                            required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Masukkan barcode produk">
                    </div>

                    {{-- Produk --}}
                    <div class="mb-4">
                        <label for="produk" class="block text-sm font-medium text-gray-700">Produk</label>

                        <select name="produk" id="produk"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>

                            <option value="" disabled>Pilih Produk</option>

                            @foreach ($products as $p)
                                <option value="{{ $p->id }}"
                                    data-barcode="{{ $p->barcode }}"
                                    {{ $productBatch->product_id == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kode Batch --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Batch Number</label>
                        <div class="flex gap-2">
                            <input type="text" name="batch_number" id="batch_number"
                                   maxlength="5" minlength="5"
                                   required
                                   value="{{ old('batch_number', $productBatch->batch_number) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">

                            <button type="button"
                                    onclick="generateBatchCode()"
                                    class="mt-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Generate
                            </button>
                        </div>
                    </div>
                    {{-- Harga Beli --}}
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Harga Beli</label>

    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>

        {{-- DISPLAY (TANPA VALUE DARI BLADE) --}}
        <input
            type="text"
            id="harga_display"
            class="mt-1 block w-full px-3 py-2 pl-10 border rounded-md shadow-sm
                   focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
            placeholder="0"
        >

        {{-- VALUE ASLI (INTEGER) --}}
        <input
            type="hidden"
            name="harga_beli"
            id="harga_beli"
            value="{{ old('harga_beli', $productBatch->harga_beli) }}"
            required
        >
    </div>

    @error('harga_beli')
        <p class="text-red-600 text-sm">{{ $message }}</p>
    @enderror
</div>

                    {{-- Tanggal Masuk --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk"
                               value="{{ old('tanggal_masuk', $productBatch->tanggal_masuk) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Tanggal Expired --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Expired</label>

                        <input type="date" name="tanggal_expired" id="tanggal_expired"
                               value="{{ old('tanggal_expired', $productBatch->tanggal_expired) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                               {{ $productBatch->tanggal_expired ? '' : 'disabled' }}>
                    </div>

                    {{-- Qty Masuk --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kuantitas Masuk</label>
                        <input type="number" id="qty_masuk" name="quantity_masuk"
                               required
                               value="{{ old('quantity_masuk', $productBatch->quantity_masuk) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Qty Sekarang --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Kuantitas Sekarang</label>
                        <input type="number" id="qty_sekarang" name="quantity_sekarang" readonly
                               value="{{ old('quantity_sekarang', $productBatch->quantity_sekarang) }}"
                               class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Supplier --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <input type="text" name="supplier"
                               value="{{ old('supplier', $productBatch->supplier) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="active" {{ $productBatch->status == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="expired" {{ $productBatch->status == 'expired' ? 'selected' : '' }}>Kadaluwarsa</option>
                            <option value="sold_out" {{ $productBatch->status == 'sold_out' ? 'selected' : '' }}>Habis Terjual</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
    function generateBatchCode() {
        const code = Math.floor(Math.random() * 100000)
            .toString()
            .padStart(5, '0');

        document.getElementById('batch_number').value = code;
    }

    const qtyMasukInput = document.getElementById('qty_masuk');
    const qtySekarangInput = document.getElementById('qty_sekarang');

    const qtyMasukAwal = parseInt(qtyMasukInput.value) || 0;
    const qtySekarangAwal = parseInt(qtySekarangInput.value) || 0;

    qtyMasukInput.addEventListener('input', function () {
        const qtyMasukBaru = parseInt(this.value) || 0;

        const selisih = qtyMasukBaru - qtyMasukAwal;

        const qtySekarangBaru = qtySekarangAwal + selisih;

        qtySekarangInput.value = qtySekarangBaru >= 0 ? qtySekarangBaru : 0;
    });

    const tanggalMasuk = document.querySelector('input[name="tanggal_masuk"]');
    const tanggalExpired = document.getElementById('tanggal_expired');

    @if($productBatch->tanggal_expired)
        tanggalExpired.disabled = false;
        tanggalExpired.min = tanggalMasuk.value;
    @else
        tanggalExpired.disabled = true;
    @endif

    tanggalMasuk.addEventListener('change', function () {
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

    document.getElementById('barcode').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            let inputBarcode = this.value.trim();
            let produkSelect = document.getElementById('produk');

            let found = false;

            for (let opt of produkSelect.options) {
                if (opt.dataset.barcode === inputBarcode) {
                    opt.selected = true;
                    found = true;
                    break;
                }
            }

            if (!found) {
                alert("Produk dengan barcode tersebut tidak ditemukan!");
            }
        }
    });
     function formatRupiah(angka) {
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    const hargaDisplay = document.getElementById('harga_display');
    const hargaHidden  = document.getElementById('harga_beli');

    // SET NILAI AWAL (EDIT MODE)
    if (hargaHidden.value) {
        let cleanValue = hargaHidden.value
            .toString()
            .split('.')[0]; // BUANG DESIMAL .00

        hargaHidden.value = cleanValue;
        hargaDisplay.value = formatRupiah(cleanValue);
    }


    // BLOK INPUT SELAIN ANGKA
    hargaDisplay.addEventListener('keydown', function (e) {
        if (
            !/[0-9]/.test(e.key) &&
            !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)
        ) {
            e.preventDefault();
        }
    });

    // FORMAT SAAT INPUT
    hargaDisplay.addEventListener('input', function () {
        let value = this.value.replace(/[^0-9]/g, '');

        hargaHidden.value = value;
        this.value = value ? formatRupiah(value) : '';
    });

    // VALIDASI SUBMIT
    document.querySelector('form').addEventListener('submit', function (e) {
        if (!hargaHidden.value || hargaHidden.value === '0') {
            e.preventDefault();
            alert('Harga beli harus diisi!');
        }
    });

      const barcodeInput = document.getElementById('barcode');
    const produkSelect = document.getElementById('produk');

    // === PRODUK → BARCODE ===
    produkSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const barcode = selectedOption.dataset.barcode || '';

        barcodeInput.value = barcode;
    });
</script>
@endpush
