@extends('layouts.app')

@section('title', __('Buat Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Buat Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		<div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
			<div class="max-w-auto">
				<h3 class="mb-4">{{ __('Buat Surat Jalan') }}</h3>
				@if ($errors->any())
				<div class="mb-4 p-3 rounded bg-red-50 text-red-700">
					<ul class="list-disc list-inside text-sm">
						@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
				@endif

				<form method="POST" action="{{ route('surat-jalan.store') }}" enctype="multipart/form-data">
					@csrf
					<div class="mb-4">
						<label for="invoice_id" class="block text-sm font-medium text-gray-700">{{ __('Invoice') }}</label>
						<select name="invoice_id" id="invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
							<option value="" disabled selected>{{ __('Pilih Invoice') }}</option>
							@foreach($invoices as $inv)
							<option value="{{ $inv->id }}" data-grand="{{ $inv->grand_total }}" data-customer="{{ $inv->customer_id }}" data-date="{{ $inv->tanggal_invoice }}">{{ $inv->invoice_number }} — {{ $inv->customer->nama_customer ?? '-' }}</option>
							@endforeach
						</select>
						<p id="invoice-grand" class="mt-1 text-xs text-gray-600"></p>
						<input type="hidden" name="customer_id" id="customer_id">
					</div>

					<div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
						<div>
							<label for="tanggal" class="block text-sm font-medium text-gray-700">{{ __('Tanggal') }}</label>
							<input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
						</div>
						<div>
							<label for="nomor_surat_jalan" class="block text-sm font-medium text-gray-700">{{ __('Nomor Surat Jalan') }}</label>
							<div class="mt-1 flex gap-2">
								<input type="text" name="nomor_surat_jalan" id="nomor_surat_jalan" value="{{ old('nomor_surat_jalan') }}" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
								<button type="button" id="gen-no" class="px-3 py-1.5 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">{{ __('Generate') }}</button>
							</div>
						</div>
					</div>



					<input type="hidden" id="ongkos_kirim_display" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />


					<div class="mb-4">
						<label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status Pengiriman') }}</label>
						<select name="status" id="status" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
							<option value="belum dikirim">Belum Dikirim</option>
							<option value="sudah dikirim">Sudah Dikirim</option>
							<option value="cancel">Dibatalkan</option>
						</select>
					</div>
					{{-- Foto Produk --}}
					<div class="mb-4">
						<label for="bukti_pengiriman" class="block text-sm font-medium text-gray-700">
							{{ __('Bukti Pengiriman') }}
							<span id="bukti-required-indicator" class="text-red-500 hidden">*</span>
						</label>

						<input type="file" name="bukti_pengiriman" id="bukti_pengiriman" accept="image/*"
							class="mt-1 block w-full border-gray-300 rounded-md shadow-sm
                                          focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
						<p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, JPEG (Max: 2MB). <span class="font-semibold text-red-600">Wajib jika status "Sudah Dikirim"</span></p>

						{{-- Preview Foto --}}
						<div class="mt-3 hidden" id="preview-container">
							<p class="text-xs text-gray-600 mb-1">Preview:</p>
							<div class="relative inline-block">
								<img id="previewImage" src="" class="w-32 h-32 object-cover rounded-md border" />
								<button type="button" onclick="cancelPreviewBukti()"
									class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 text-sm">
									×
								</button>
							</div>
						</div>
					</div>

					<div class="mb-4">
						<label for="alasan_cancel" class="block text-sm font-medium text-gray-700">{{ __('Alasan Batal') }}</label>
						<input type="text" name="alasan_cancel" id="alasan_cancel" value="{{ old('alasan_cancel') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
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
		const sel = document.getElementById('invoice_id');
		const grandText = document.getElementById('invoice-grand');
		const custHidden = document.getElementById('customer_id');
		const genBtn = document.getElementById('gen-no');
		const noInput = document.getElementById('nomor_surat_jalan');
		const tanggalInput = document.getElementById('tanggal');
		const statusEl = document.getElementById('status');
		const alasanEl = document.getElementById('alasan_cancel');
		const buktiInput = document.getElementById('bukti_pengiriman');
		const buktiIndicator = document.getElementById('bukti-required-indicator');
		const formEl = document.querySelector('form');

		function formatIDR(n) {
			return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
		}

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

		// Invoice change handler
		sel.addEventListener('change', function() {
			const opt = sel.options[sel.selectedIndex];
			const invGrand = parseFloat(opt?.getAttribute('data-grand') || 0);
			const custId = opt?.getAttribute('data-customer') || '';
			grandText.textContent = invGrand ? ('Grand Total Invoice: ' + formatIDR(invGrand)) : '';
			custHidden.value = custId;

			// Set min date for SJ based on invoice date
			const invDate = opt?.getAttribute('data-date') || '';
			if (tanggalInput && invDate) {
				tanggalInput.min = invDate;
				if (tanggalInput.value && tanggalInput.value < invDate) {
					tanggalInput.value = invDate;
				}
			}
		});

		// Generate nomor surat jalan
		genBtn.addEventListener('click', function() {
			const now = new Date();
			const yy = String(now.getFullYear()).slice(-2);
			const mm = String(now.getMonth() + 1).padStart(2, '0');
			const dd = String(now.getDate()).padStart(2, '0');
			const datePart = yy + mm + dd;
			const randomPart = Math.random()
				.toString(36)
				.substring(2, 7)
				.toUpperCase();

			noInput.value = `SJ-${datePart}-${randomPart}`;

			Swal.fire({
				toast: true,
				position: 'top-end',
				icon: 'success',
				title: 'Nomor surat jalan berhasil digenerate',
				showConfirmButton: false,
				timer: 2000,
				timerProgressBar: true
			});
		});

		// Toggle required indicators based on status
		function toggleRequiredFields() {
			const status = statusEl.value;
			const isCancelled = status === 'cancel';
			const isSudahDikirim = status === 'sudah dikirim';

			// Alasan cancel required if cancelled
			if (isCancelled) {
				alasanEl.setAttribute('required', 'required');
			} else {
				alasanEl.removeAttribute('required');
			}

			// Bukti pengiriman indicator (visual only, validation in form submit)
			if (isSudahDikirim) {
				buktiIndicator.classList.remove('hidden');
			} else {
				buktiIndicator.classList.add('hidden');
			}
		}

		if (statusEl) {
			statusEl.addEventListener('change', function() {
				toggleRequiredFields();

				// Show info toast
				if (this.value === 'sudah dikirim') {
					Swal.fire({
						toast: true,
						position: 'top-end',
						icon: 'info',
						title: 'Status "Sudah Dikirim"',
						text: 'Bukti pengiriman wajib diisi',
						showConfirmButton: false,
						timer: 3000,
						timerProgressBar: true
					});
				}
			});
		}

		// Handle file input for bukti pengiriman
		const previewContainer = document.getElementById('preview-container');
		const previewImage = document.getElementById('previewImage');

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

					Swal.fire({
						toast: true,
						position: 'top-end',
						icon: 'success',
						title: 'Bukti pengiriman berhasil dipilih',
						showConfirmButton: false,
						timer: 2000,
						timerProgressBar: true
					});
				} else {
					previewContainer.classList.add('hidden');
					previewImage.src = "";
				}
			});
		}

		// Form validation before submit
		if (formEl) {
			formEl.addEventListener('submit', function(e) {
				const status = statusEl.value;
				const hasFile = buktiInput.files.length > 0;

				// Validate: status "sudah dikirim" wajib ada bukti
				if (status === 'sudah dikirim' && !hasFile) {
					e.preventDefault();
					Swal.fire({
						icon: 'error',
						title: 'Bukti Pengiriman Diperlukan',
						text: 'Bukti pengiriman wajib diisi jika status "Sudah Dikirim".',
						confirmButtonColor: '#2563eb'
					});
					buktiInput.focus();
					return false;
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

		// Initialize min date if invoice preselected
		if (tanggalInput && sel && sel.selectedIndex > 0) {
			const opt = sel.options[sel.selectedIndex];
			const invDate = opt?.getAttribute('data-date') || '';
			if (invDate) {
				tanggalInput.min = invDate;
				if (tanggalInput.value && tanggalInput.value < invDate) {
					tanggalInput.value = invDate;
				}
			}
		}

		// Init on load
		toggleRequiredFields();
	})();

	// Function to cancel preview (outside closure)
	function cancelPreviewBukti() {
		document.getElementById('bukti_pengiriman').value = '';
		document.getElementById('preview-container').classList.add('hidden');

		Swal.fire({
			toast: true,
			position: 'top-end',
			icon: 'info',
			title: 'Bukti pengiriman dibatalkan',
			showConfirmButton: false,
			timer: 2000,
			timerProgressBar: true
		});
	}
</script>
@endpush
