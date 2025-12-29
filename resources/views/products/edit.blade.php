@extends('layouts.app')

@section('title', __('Edit Produk'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Produk') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">

                    <h3 class="mb-4">{{ __('Edit Produk') }}</h3>

                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Nama Produk --}}
                        <div class="mb-4">
                            <label for="nama_produk" class="block text-sm font-medium text-gray-700">
                                {{ __('Nama Produk') }}
                            </label>
                            <input type="text" name="nama_produk" id="nama_produk"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                value="{{ old('nama_produk', $product->nama_produk) }}" required>
                        </div>

                        {{-- Barcode + Generate --}}
                        <div class="mb-4">
                            <label for="barcode" class="block text-sm font-medium text-gray-700">
                                {{ __('Barcode') }}
                            </label>

                            <div class="flex gap-2">
                                <input type="text" name="barcode" id="barcode"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                              focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    value="{{ old('barcode', $product->barcode) }}" required>

                                {{-- Tombol Generate --}}
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
                                value="{{ old('kategori', $product->kategori) }}" required>
                        </div>

                        {{-- Harga --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Harga</label>

                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>

                                <!-- Input Display -->
                                <input type="text" id="harga_display"
                                    class="mt-1 block w-full px-3 py-2 pl-10 border rounded-md shadow-sm
                                        focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                    placeholder="0">


                                <!-- Input Hidden -->
                                <input type="hidden" name="harga" id="harga"
                                    value="{{ old('harga', $product->harga) }}" required>
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
                                value="{{ old('satuan', $product->satuan) }}" required>
                        </div>

                        {{-- Foto Produk --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Foto Produk</label>

                            {{-- Foto Lama --}}
                            <div class="mb-3">
                                <img id="previewImage" src="{{ asset('storage/' . $product->foto_produk) }}"
                                    class="w-32 h-32 object-cover rounded-md border" alt="Foto Produk">
                            </div>

                            {{-- Input File --}}
                            <input type="file" name="foto_produk" id="foto_produk" accept="image/*"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm">

                            <small class="text-gray-600">Pilih foto baru untuk mengganti foto saat ini.</small>
                        </div>

                        {{-- Min Stok Alert --}}
                        <div class="mb-4">
                            <label for="min_stok_alert" class="block text-sm font-medium text-gray-700">
                                {{ __('Min Pemberitahuan Stok') }}
                            </label>
                            <input type="number" name="min_stok_alert" id="min_stok_alert"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                value="{{ old('min_stok_alert', $product->min_stok_alert) }}" required>
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
                                <option disabled>Pilih Status</option>
                                <option value="available"
                                    {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>
                                    Tersedia
                                </option>
                                <option value="unavailable"
                                    {{ old('status', $product->status) == 'unavailable' ? 'selected' : '' }}>
                                    Tidak Tersedia
                                </option>
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
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
        function formatRupiah(angka) {
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        const hargaDisplay = document.getElementById('harga_display');
        const hargaHidden = document.getElementById('harga');

        // === SET NILAI AWAL (EDIT MODE) ===
        if (hargaHidden.value) {
            let cleanValue = hargaHidden.value
                .toString()
                .split('.')[0]; // BUANG DESIMAL .00

            hargaHidden.value = cleanValue;
            hargaDisplay.value = formatRupiah(cleanValue);
        }

        // === BLOK INPUT SELAIN ANGKA ===
        hargaDisplay.addEventListener('keydown', function(e) {
            if (
                !/[0-9]/.test(e.key) &&
                !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)
            ) {
                e.preventDefault();
            }
        });

        // === FORMAT SAAT INPUT ===
        hargaDisplay.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/g, '');

            hargaHidden.value = value;
            this.value = value ? formatRupiah(value) : '';
        });

        // === VALIDASI SUBMIT ===
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!hargaHidden.value || hargaHidden.value === '0') {
                e.preventDefault();
                alert('Harga harus diisi!');
            }
        });
    </script>
@endpush
