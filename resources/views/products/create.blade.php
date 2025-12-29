@extends('layouts.app')

@section('title', __('Tambah Produk'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Tambah Produk') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">

                <h3 class="mb-4">{{ __('Tambah Produk') }}</h3>

                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Nama Produk --}}
                    <div class="mb-4">
                        <label for="nama_produk" class="block text-sm font-medium text-gray-700">
                            {{ __('Nama Produk') }}
                        </label>
                        <input type="text" name="nama_produk" id="nama_produk"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>
                    </div>

                    {{-- Barcode --}}
                    <div class="mb-4">
                        <label for="barcode" class="block text-sm font-medium text-gray-700">
                            {{ __('Barcode') }}
                        </label>

                        <div class="flex gap-2">
                            <input type="text" name="barcode" id="barcode"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                              focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                required>

                            <button type="button" onclick="generateBarcode()"
                                class="mt-1 px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Generate
                            </button>
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label for="kategori" class="block text-sm font-medium text-gray-700">
                            {{ __('Kategori') }}
                        </label>
                        <input type="text" name="kategori" id="kategori"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>
                    </div>

                    {{-- Harga --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Harga</label>

                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>

                            <input
                                type="text"
                                id="harga_display"
                                class="mt-1 block w-full px-3 py-2 pl-10 border rounded-md shadow-sm
                                        focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                placeholder="0">


                            <input
                                type="hidden"
                                name="harga"
                                id="harga"
                                value="{{ old('harga') }}"
                                required>
                        </div>

                        @error('harga')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Satuan --}}
                    <div class="mb-4">
                        <label for="satuan" class="block text-sm font-medium text-gray-700">
                            {{ __('Satuan') }}
                        </label>
                        <input type="text" name="satuan" id="satuan"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>
                    </div>

                    {{-- Foto Produk --}}
                    <div class="mb-4">

                        {{-- Preview Foto --}}
                        <div class="mt-3">
                            <img id="previewImage"
                                src=""
                                class="hidden w-32 h-32 object-cover rounded-md border" />
                        </div>

                        <label for="foto_produk" class="block text-sm font-medium text-gray-700">
                            {{ __('Foto Produk') }}
                        </label>

                        <input type="file" name="foto_produk" id="foto_produk" accept="image/*"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>

                    </div>

                    {{-- Min Stok Alert --}}
                    <div class="mb-4">
                        <label for="min_stok_alert" class="block text-sm font-medium text-gray-700">
                            {{ __('Min Pemberitahuan Stok') }}
                        </label>
                        <input type="number" name="min_stok_alert" id="min_stok_alert"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            {{ __('Status') }}
                        </label>

                        <select name="status" id="status"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                           focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                            required>
                            <option value="" disabled selected>{{ __('Pilih Status') }}</option>
                            <option value="available">{{ __('Tersedia') }}</option>
                            <option value="unavailable">{{ __('Tidak Tersedia') }}</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{ __('Simpan') }}
                        </button>

                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            {{ __('Batal') }}
                        </a>
                    </div>

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

    // === HARGA — Format Rupiah ===
    const hargaDisplay = document.getElementById('harga_display');
    const hargaHidden = document.getElementById('harga');

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
            alert('Harga harus diisi');
        }
    });



    function generateBarcode() {
        const randomNumber = Math.floor(Math.random() * 100000000)
            .toString()
            .padStart(8, '0');

        document.getElementById('barcode').value = randomNumber;
    }

    // Preview Foto Upload
    document.getElementById('foto_produk').addEventListener('change', function(event) {
        let preview = document.getElementById('previewImage');
        let file = event.target.files[0];

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
            preview.src = "";
        }
    });
</script>
@endpush