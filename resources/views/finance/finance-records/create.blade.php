@extends('layouts.app')

@section('title', __('Tambah Data Keuangan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Tambah Data Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Tambah Data Keuangan') }}</h3>
                <form method="POST" action="{{ route('finance-records.store') }}" class="space-y-4"   enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block mb-1">Tipe Transaksi</label>
                        <select name="tipe" class="w-full px-3 py-2 border rounded bg-gray-100 cursor-not-allowed" required disabled>
                            <option value="expense" selected>Pengeluaran</option>
                        </select>
                        <!-- Hidden input to send the value since disabled fields don't submit -->
                        <input type="hidden" name="tipe" value="expense">
                        @error('tipe')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('tanggal')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block mb-1">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" class="w-full px-3 py-2 border rounded" placeholder="Contoh: Gaji, Belanja, Transport, dll" required />
                        @error('kategori')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Jumlah</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-500">Rp</span>
                            <input type="text" id="jumlah_display" class="w-full px-3 py-2 pl-10 border rounded" placeholder="0" />
                            <input type="hidden" name="jumlah" id="jumlah" value="{{ old('jumlah') }}" required />
                        </div>
                        @error('jumlah')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    {{-- Nota --}}
                        <div class="mb-4">

                            {{-- Preview Foto --}}
                            <div class="mt-3">
                                <img id="previewImage"
                                     src=""
                                     class="hidden w-32 h-32 object-cover rounded-md border" />
                            </div>

                            <label for="foto_nota" class="block text-sm font-medium text-gray-700">
                                {{ __('Nota') }}
                            </label>

                            <input type="file" name="foto_nota" id="foto_nota" accept="image/*"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                   required>

                        </div>
                    <div>
                        <label class="block mb-1">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="3" class="w-full px-3 py-2 border rounded" placeholder="Catatan tambahan...">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Simpan</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('finance-records.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const jumlahDisplay = document.getElementById('jumlah_display');
    const jumlahHidden = document.getElementById('jumlah');

    // Format number with thousand separator (dots)
    function formatRupiah(angka) {
        // Convert to string and remove any non-digit characters first
        const numberString = angka.toString().replace(/\D/g, '');

        // Return empty if no valid number
        if (!numberString) return '';

        // Add thousand separators
        return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // Remove all non-numeric characters (keep only digits)
    function unformatRupiah(rupiahString) {
        // Remove dots (thousand separator), commas, and any other non-digit characters
        return rupiahString.replace(/\D/g, '');
    }

    // Initialize display on page load (for validation errors with old() values)
    function initializeDisplay() {
        const rawValue = jumlahHidden.value;

        if (rawValue) {
            // Parse as float first to handle decimal values
            const floatValue = parseFloat(rawValue);

            // Round to nearest integer (no decimal places)
            const cleanValue = Math.round(floatValue).toString();

            // Update both fields
            jumlahHidden.value = cleanValue;
            jumlahDisplay.value = formatRupiah(cleanValue);

            console.log('[CREATE FINANCE] Initial raw value:', rawValue);
            console.log('[CREATE FINANCE] Parsed float:', floatValue);
            console.log('[CREATE FINANCE] Clean value (rounded):', cleanValue);
            console.log('[CREATE FINANCE] Formatted display:', formatRupiah(cleanValue));
        }
    }

    // Handle input changes
    jumlahDisplay.addEventListener('input', function(e) {
        // Get the raw input value
        const inputValue = e.target.value;

        // Remove all formatting to get clean number
        const cleanValue = unformatRupiah(inputValue);

        // Update hidden field with clean number
        jumlahHidden.value = cleanValue;

        // Format and display
        if (cleanValue) {
            // Save cursor position
            const cursorPos = e.target.selectionStart;
            const oldLength = inputValue.length;

            // Apply formatting
            const formattedValue = formatRupiah(cleanValue);
            e.target.value = formattedValue;

            // Restore cursor position (accounting for added dots)
            const newLength = formattedValue.length;
            const newCursorPos = cursorPos + (newLength - oldLength);
            e.target.setSelectionRange(newCursorPos, newCursorPos);
        } else {
            e.target.value = '';
        }

        console.log('[CREATE FINANCE] Input:', inputValue, '→ Clean:', cleanValue, '→ Display:', e.target.value, '→ Hidden:', jumlahHidden.value);
    });

    // Validate before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const value = jumlahHidden.value;

        if (!value || value === '0' || value === '') {
            e.preventDefault();
            alert('Jumlah harus diisi dengan nilai yang valid');
            return false;
        }

        console.log('[CREATE FINANCE] Submitting value:', value);
    });

    // Initialize when page loads
    initializeDisplay();
});

 // Preview Foto Upload
    document.getElementById('foto_nota').addEventListener('change', function(event) {
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
