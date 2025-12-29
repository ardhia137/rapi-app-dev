@extends('layouts.app')

@section('title', __('Edit Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
			<div class="max-w-auto">
				<h3 class="mb-4">{{ __('Edit Surat Jalan') }}</h3>
				@if ($errors->any())
				<div class="mb-4 p-3 rounded bg-red-50 text-red-700">
					<ul class="list-disc list-inside text-sm">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<form method="POST" action="{{ route('surat-jalan.update', $suratJalan->id) }}" enctype="multipart/form-data">
					@csrf
					@method('PUT')

					{{-- Info: Field readonly karena auto-generate dari invoice --}}
					<div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
						<p class="text-sm text-blue-700">
							<strong>Info:</strong> Surat jalan ini otomatis dibuat dari invoice.
							Anda hanya perlu mengubah <strong>Status Pengiriman</strong> dan upload <strong>Bukti Pengiriman</strong>.
						</p>
					</div>

					<div class="mb-4">
						<label for="invoice_display" class="block text-sm font-medium text-gray-700">{{ __('Invoice') }}</label>
						<input type="text" id="invoice_display"
							value="{{ $suratJalan->invoice->invoice_number ?? '-' }} — {{ $suratJalan->customer->nama_customer ?? '-' }}"
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 sm:text-sm"
							readonly disabled>
						<input type="hidden" name="invoice_id" value="{{ $suratJalan->invoice_id }}">
						<input type="hidden" name="customer_id" value="{{ $suratJalan->customer_id }}">
						<p class="mt-1 text-xs text-gray-500">Grand Total: Rp {{ number_format($suratJalan->invoice->grand_total ?? 0, 0, ',', '.') }}</p>
					</div>

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="tanggal" class="block text-sm font-medium text-gray-700">{{ __('Tanggal') }}</label>
							<input type="date" name="tanggal" id="tanggal"
								value="{{ old('tanggal', $suratJalan->tanggal) }}"
								class="mt-1 block w-full border-gray-300 rounded-md shadow-sm sm:text-sm">
						</div>
						<div>
							<label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700">{{ __('Nomor Surat Jalan') }}</label>
							<input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan"
								value="{{ old('nomor_surat_jalan', $suratJalan->nomor_surat_jalan) }}"
								class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 sm:text-sm"
								readonly>
						</div>
					</div>


					<div class="mb-4">
						<label for="status" class="block text-sm font-medium text-gray-700">
							{{ __('Status Pengiriman') }} <span class="text-red-500">*</span>
						</label>
						<select name="status" id="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
							<option value="belum dikirim" @selected(old('status', $suratJalan->status)=='belum dikirim')>Belum Dikirim</option>
							<option value="sudah dikirim" @selected(old('status', $suratJalan->status)=='sudah dikirim')>Sudah Dikirim</option>
							<option value="cancel" @selected(old('status', $suratJalan->status)=='cancel')>Dibatalkan</option>
						</select>
					</div>

					<div class="mb-4" id="alasan-cancel-wrapper" style="display:{{ old('status', $suratJalan->status) == 'cancel' ? '' : 'none' }};">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">
							{{ __('Alasan Batal') }} <span class="text-red-500">*</span>
						</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel"
							value="{{ old('alasan_cancel', $suratJalan->alasan_cancel) }}"
							class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
							placeholder="Masukkan alasan pembatalan...">
					</div>

					{{-- bukti --}}
					<div class="mb-4">
						<label class="block text-sm font-medium text-gray-700">
							Bukti Pengiriman
							<span id="bukti-required-indicator" class="text-red-500 hidden">*</span>
						</label>

						@if($suratJalan->bukti_pengiriman)
							{{-- Bukti Lama --}}
							<div class="mb-3" id="current-bukti">
								<p class="text-xs text-gray-600 mb-1">Bukti pengiriman saat ini:</p>
								<div class="relative inline-block">
									<img id="currentImage" src="{{ asset('storage/' . $suratJalan->bukti_pengiriman) }}"
										class="w-32 h-32 object-cover rounded-md border cursor-pointer hover:opacity-80 transition"
										onclick="previewFullImageBukti('{{ asset('storage/' . $suratJalan->bukti_pengiriman) }}')"
										alt="Bukti Pengiriman">
									<div class="absolute top-1 right-1">
										<span class="bg-green-500 text-white text-xs px-2 py-0.5 rounded">✓ Ada</span>
									</div>
								</div>
							</div>
						@endif

						{{-- Input File --}}
						<input type="file" name="bukti_pengiriman" id="bukti_pengiriman" accept="image/*"
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
						<p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB). <span class="font-semibold text-red-600">Wajib jika status "Sudah Dikirim"</span></p>

						{{-- Preview New Image --}}
						<div class="mt-3 hidden" id="preview-container">
							<p class="text-xs text-gray-600 mb-1">Preview gambar baru:</p>
							<div class="relative inline-block">
								<img id="previewImage" src="" class="w-32 h-32 object-cover rounded-md border" />
								<button type="button" onclick="cancelPreviewBuktiEdit()"
									class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm">
									×
								</button>
							</div>
						</div>
					</div>


					<div class="flex items-center gap-4">
						<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
						<a href="{{ route('surat-jalan.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
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
		const statusEl = document.getElementById('status');
		const alasanEl = document.getElementById('alasan_cancel');
		const buktiInput = document.getElementById('bukti_pengiriman');
		const buktiIndicator = document.getElementById('bukti-required-indicator');
		const formEl = document.querySelector('form');
		const hasExistingBukti = {{ $suratJalan->bukti_pengiriman ? 'true' : 'false' }};

		// Show errors with SweetAlert
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

		function toggleRequiredFieldsEdit() {
			const status = statusEl.value;
			const isCancelled = status === 'cancel';
			const isSudahDikirim = status === 'sudah dikirim';
			const alasanWrapper = document.getElementById('alasan-cancel-wrapper');

			// Alasan cancel - show/hide wrapper
			if (isCancelled) {
				if (alasanWrapper) alasanWrapper.style.display = '';
				if (alasanEl) alasanEl.setAttribute('required', 'required');
			} else {
				if (alasanWrapper) alasanWrapper.style.display = 'none';
				if (alasanEl) {
					alasanEl.removeAttribute('required');
					alasanEl.value = ''; // Clear value
				}
			}

			// Bukti pengiriman indicator
			if (isSudahDikirim) {
				buktiIndicator.classList.remove('hidden');
			} else {
				buktiIndicator.classList.add('hidden');
			}
		}

		statusEl.addEventListener('change', function() {
			toggleRequiredFieldsEdit();

			// Show info toast
			if (this.value === 'sudah dikirim') {
				Swal.fire({
					toast: true,
					position: 'top-end',
					icon: 'info',
					title: 'Status "Sudah Dikirim"',
					text: 'Bukti pengiriman wajib ada',
					showConfirmButton: false,
					timer: 3000,
					timerProgressBar: true
				});
			}
		});

		// Handle file input
		const previewContainer = document.getElementById('preview-container');
		const previewImage = document.getElementById('previewImage');
		const currentBukti = document.getElementById('current-bukti');

		if (buktiInput && previewImage) {
			buktiInput.addEventListener('change', function(event) {
				const file = event.target.files[0];

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

					previewImage.src = URL.createObjectURL(file);
					previewContainer.classList.remove('hidden');
					if (currentBukti) currentBukti.classList.add('opacity-50');

					Swal.fire({
						toast: true,
						position: 'top-end',
						icon: 'success',
						title: 'Bukti pengiriman baru dipilih',
						showConfirmButton: false,
						timer: 2000,
						timerProgressBar: true
					});
				} else {
					previewContainer.classList.add('hidden');
					if (currentBukti) currentBukti.classList.remove('opacity-50');
					previewImage.src = "";
				}
			});
		}

		// Form validation before submit
		if (formEl) {
			formEl.addEventListener('submit', function(e) {
				const status = statusEl.value;
				const hasNewFile = buktiInput.files.length > 0;

				// Validate: status "sudah dikirim" wajib ada bukti (new atau existing)
				if (status === 'sudah dikirim') {
					if (!hasNewFile && !hasExistingBukti) {
						e.preventDefault();
						Swal.fire({
							icon: 'error',
							title: 'Bukti Pengiriman Diperlukan',
							text: 'Bukti pengiriman wajib ada jika status "Sudah Dikirim".',
							confirmButtonColor: '#2563eb'
						});
						buktiInput.focus();
						return false;
					}
				}

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
			});
		}

		// Initialize toggle on page load
		toggleRequiredFieldsEdit();
	})();

	// Helper functions (outside closure)
	function previewFullImageBukti(imageUrl) {
		Swal.fire({
			imageUrl: imageUrl,
			imageAlt: 'Bukti Pengiriman',
			showConfirmButton: false,
			showCloseButton: true,
			width: 'auto',
			customClass: {
				image: 'max-h-96'
			}
		});
	}

	function cancelPreviewBuktiEdit() {
		document.getElementById('bukti_pengiriman').value = '';
		document.getElementById('preview-container').classList.add('hidden');
		const currentBukti = document.getElementById('current-bukti');
		if (currentBukti) currentBukti.classList.remove('opacity-50');

		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: 'info',
			title: 'Bukti pengiriman baru dibatalkan',
			showConfirmButton: false,
			timer: 2000,
			timerProgressBar: true
		});
	}
</script>
@endpush
