@extends('layouts.app')

@section('title', __('Setor Penjualan'))


@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Setor Penjualan') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-auto">
                    <h3 class="mb-4">{{ __('Setor Penjualan') }}</h3>

                    <div class="mb-6 border rounded p-4 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600">{{ __('Nomor Invoice') }}</div>
                                <div class="font-semibold">{{ $invoice->invoice_number }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Pelanggan') }}</div>
                                <div class="font-semibold">{{ $invoice->customer->nama_customer ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Tanggal Invoice') }}</div>
                                <div class="font-semibold">{{ $invoice->tanggal_invoice }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Grand Total') }}</div>
                                <div class="font-semibold">Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Metode Pembayaran') }}</div>
                                <div class="font-semibold">{{ ucfirst($invoice->metode_pembayaran ?? '-') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">{{ __('Status Pembayaran') }}</div>
                                <div class="font-semibold">
                                    <span class="px-2 py-1 text-xs rounded
                                        {{ $invoice->status_pembayaran === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($invoice->status_pembayaran) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('invoices.setor.update', $invoice) }}" enctype="multipart/form-data" id="setorForm">
                        @csrf
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="status_setor" class="block text-sm font-medium text-gray-700">
                                    {{ __('Status Setor') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status_setor" id="status_setor" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                    <option value="belum" @selected(old('status_setor', $invoice->status_setor) == 'belum')>Belum</option>
                                    <option value="sudah" @selected(old('status_setor', $invoice->status_setor) == 'sudah')>Sudah</option>
                                </select>
                            </div>
                            <div>
                                <label for="bukti_setor" class="block text-sm font-medium text-gray-700">
                                    {{ __('Bukti Setor') }} <span id="required_indicator" class="text-red-500 {{ old('status_setor', $invoice->status_setor) == 'sudah' ? '' : 'hidden' }}">*</span>
                                </label>
                                <input type="file" name="bukti_setor" id="bukti_setor" accept="image/*" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB)</p>
                                @if($invoice->bukti_setor)
                                    <div class="mt-2" id="currentBuktiSetor">
                                        <p class="text-xs text-gray-600 mb-1">Bukti setor saat ini:</p>
                                        <div class="relative inline-block">
                                            <img src="{{ asset('storage/'.$invoice->bukti_setor) }}" alt="Bukti Setor" class="h-32 rounded border cursor-pointer hover:opacity-80 transition" onclick="previewImage('{{ asset('storage/'.$invoice->bukti_setor) }}')">
                                            <div class="absolute top-1 right-1">
                                                <span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded">✓ Ada</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-2 hidden" id="newImagePreview">
                                    <p class="text-xs text-gray-600 mb-1">Preview gambar baru:</p>
                                    <div class="relative inline-block">
                                        <img id="previewImg" src="" alt="Preview" class="h-32 rounded border">
                                        <button type="button" onclick="cancelNewImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 p-3 rounded bg-blue-50 text-blue-700 text-sm">
                            <strong>📌 Catatan Penting:</strong>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Status "Sudah" hanya dapat dipilih jika Anda mengunggah bukti setor.</li>
                                <li>Bukti setor diperlukan untuk semua metode pembayaran (Tunai, Transfer, E-wallet, dll).</li>
                                <li>Pastikan bukti setor jelas dan dapat dibaca.</li>
                            </ul>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                <i class="fas fa-save mr-1"></i> {{ __('Simpan Setor') }}
                            </button>
                            <a href="{{ route('invoices.setor') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                                <i class="fas fa-arrow-left mr-1"></i> {{ __('Kembali') }}
                            </a>
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
document.addEventListener('DOMContentLoaded', function() {
    const statusSetorSelect = document.getElementById('status_setor');
    const buktiSetorInput = document.getElementById('bukti_setor');
    const requiredIndicator = document.getElementById('required_indicator');
    const setorForm = document.getElementById('setorForm');
    const hasExistingBukti = {{ $invoice->bukti_setor ? 'true' : 'false' }};

    // Show success message if exists
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#2563eb',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    // Show error messages if exists
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: `
                <div class="text-left">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            confirmButtonColor: '#2563eb'
        });
    @endif

    // Toggle required indicator based on status setor
    statusSetorSelect.addEventListener('change', function() {
        if (this.value === 'sudah') {
            requiredIndicator.classList.remove('hidden');

            // Show info alert
            Swal.fire({
                icon: 'info',
                title: 'Perhatian',
                text: 'Status "Sudah" memerlukan bukti setor. Pastikan Anda mengunggah bukti setor yang jelas.',
                confirmButtonColor: '#2563eb',
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            requiredIndicator.classList.add('hidden');
        }
    });

    // Preview image on file select
    buktiSetorInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
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
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('newImagePreview').classList.remove('hidden');
                if (document.getElementById('currentBuktiSetor')) {
                    document.getElementById('currentBuktiSetor').classList.add('opacity-50');
                }
            };
            reader.readAsDataURL(file);

            // Show success toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Gambar berhasil dipilih',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    });

    // Validasi form sebelum submit dengan SweetAlert
    setorForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const statusSetor = statusSetorSelect.value;
        const hasNewFile = buktiSetorInput.files.length > 0;

        // Validasi: Jika status "sudah", wajib ada bukti
        if (statusSetor === 'sudah') {
            if (!hasNewFile && !hasExistingBukti) {
                Swal.fire({
                    icon: 'error',
                    title: 'Bukti Setor Diperlukan',
                    text: 'Bukti setor wajib diisi jika status setor adalah "Sudah".',
                    confirmButtonColor: '#2563eb'
                });
                buktiSetorInput.focus();
                return false;
            }
        }

        // Konfirmasi sebelum submit
        Swal.fire({
            title: 'Konfirmasi Setor',
            html: `
                <div class="text-left">
                    <p class="mb-2">Apakah Anda yakin ingin menyimpan data setor dengan detail berikut?</p>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li><strong>Invoice:</strong> {{ $invoice->invoice_number }}</li>
                        <li><strong>Status Setor:</strong> <span class="font-semibold ${statusSetor === 'sudah' ? 'text-green-600' : 'text-yellow-600'}">${statusSetor === 'sudah' ? 'Sudah' : 'Belum'}</span></li>
                        <li><strong>Bukti Setor:</strong> ${hasNewFile ? 'File baru akan diupload' : (hasExistingBukti ? 'Menggunakan bukti yang sudah ada' : 'Tidak ada bukti')}</li>
                    </ul>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menyimpan...',
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                setorForm.submit();
            }
        });
    });
});

// Function to preview full image
function previewImage(imageUrl) {
    Swal.fire({
        imageUrl: imageUrl,
        imageAlt: 'Bukti Setor',
        showConfirmButton: false,
        showCloseButton: true,
        width: 'auto',
        customClass: {
            image: 'max-h-96'
        }
    });
}

// Function to cancel new image selection
function cancelNewImage() {
    document.getElementById('bukti_setor').value = '';
    document.getElementById('newImagePreview').classList.add('hidden');
    if (document.getElementById('currentBuktiSetor')) {
        document.getElementById('currentBuktiSetor').classList.remove('opacity-50');
    }

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: 'Gambar baru dibatalkan',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
}
</script>
@endpush
