@extends('layouts.app')

@section('title', __('Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- HEADER STATISTIC + FILTER --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="text-sm text-green-700 mb-1">Rasio Lunas / Transaksi</div>
                <div class="text-4xl font-extrabold text-green-800 tracking-tight">
                    {{ number_format($paidCount ?? 0, 0, ',', '.') }}
                    <span class="text-green-600">/</span>
                    {{ number_format($totalCount ?? 0, 0, ',', '.') }}
                </div>
                <div class="mt-2 text-xs text-green-700 space-y-0.5">
                    <div>Lunas: <span class="font-semibold">{{ number_format($paidCount ?? 0, 0, ',', '.') }}</span> transaksi</div>
                    <div>Total transaksi: <span class="font-semibold">{{ number_format($totalCount ?? 0, 0, ',', '.') }}</span></div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                    <div class="text-[11px] text-green-600">Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}</div>
                    @endif
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('surat-jalan.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                            <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                            <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg">
                        </div>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        <a href="{{ route('surat-jalan.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- MAIN WRAPPER --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">
                {{-- HEADER TABLE --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold">Surat Jalan</h3>
                        <p class="text-xs text-gray-500 mt-1">Surat jalan otomatis dibuat saat invoice dibuat. Silakan edit untuk mengubah status pengiriman.</p>
                    </div>
                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTablesDesktop" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-center">
                                <th class="px-3 py-2 border text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-xs uppercase">Nomor Surat Jalan</th>
                                <th class="px-3 py-2 border text-xs uppercase">Pelanggan</th>
                                <th class="px-3 py-2 border text-xs uppercase">Invoice</th>
                                <th class="px-3 py-2 border text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-xs uppercase">Bukti Pengiriman</th>
                                <th class="px-3 py-2 border text-xs uppercase">Status Pembayaran</th>
                                <th class="px-3 py-2 border text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suratJalans as $sj)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border text-center">{{ $loop->iteration + ($suratJalans->currentPage() - 1) * $suratJalans->perPage() }}</td>
                                <td class="px-3 py-2 border text-left">{{ $sj->nomor_surat_jalan ?? '-' }}</td>
                                <td class="px-3 py-2 border text-left">{{ $sj->customer->nama_customer ?? '-' }}</td>
                                <td class="px-3 py-2 border text-left">{{ $sj->invoice->invoice_number ?? $sj->invoice_id }}</td>
                                <td class="px-3 py-2 border text-center">{{ $sj->tanggal }}</td>
                                <td class="border px-3 py-2 text-center">
                                    {!! $sj->bukti_pengiriman
                                    ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$sj->bukti_pengiriman).'">Lihat</a>'
                                    : '-' !!}
                                </td>

                                <td class="px-3 py-2 border text-center">
                                    @php $status = $sj->status; @endphp
                                    @if ($status === 'sudah dikirim')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Sudah Dikirim</span>
                                    @elseif ($status === 'belum dikirim')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Dikirim</span>
                                    @elseif ($status === 'cancel')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                    @else
                                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">{{ ucfirst($status ?? '-') }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 border text-center">
                                    <div class="flex flex-col space-y-1">
                                        <a href="{{ route('surat-jalan.show', $sj) }}"
                                            class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                            Detail
                                        </a>
                                        <a href="{{ route('surat-jalan.edit', $sj->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">Edit</a>
                                        <form action="{{ route('surat-jalan.destroy', $sj->id) }}" method="POST" class="inline" onclick="event.stopPropagation();" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-800 hover:underline">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden mt-4" id="mobileWrapper">

                    {{-- TOP CONTROL --}}
                    <div class="flex justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="border rounded text-sm mx-1">
                                <option value="5">5</option>
                                <option value="10">10</option>
                            </select>
                            entries
                        </div>
                    </div>

                    {{-- CARDS --}}
                    <div id="mobileCards" class="space-y-3">
                        @foreach($suratJalans as $sj)
                        <div class="mobile-card border rounded-lg bg-white shadow">
                            {{-- BARCODE --}}
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="font-semibold">{{ $sj->nomor_surat_jalan ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $sj->tanggal }}</div>
                            </div>

                            <div class="px-4 py-3 text-sm space-y-1">
                                <div>Pelanggan: <b>{{ $sj->customer->nama_customer ?? '-' }}</b></div>
                                <div>Invoice: <b>{{ $sj->invoice->invoice_number ?? $sj->invoice_id }}</b></div>
                                <div>Status:
                                    <b class="
                                        {{
                                            $sj->status === 'sudah dikirim' ? 'text-green-700' :
                                            ($sj->status === 'belum dikirim' ? 'text-red-700' :
                                            ($sj->status === 'cancel' ? 'text-gray-500' : 'text-gray-700'))
                                        }}
                                    ">


                                        {{ ucfirst($sj->status ?? '-') }}
                                    </b>
                                </div>
                                <div>Bukti Pengiriman: {!! $sj->bukti_pengiriman
                                    ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$sj->bukti_pengiriman).'">Lihat</a>'
                                    : '-' !!}</div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                <a href="{{ route('surat-jalan.show', $sj->id) }}"
                                    class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2">
                                    Detail
                                </a>
                                <a href="{{ route('surat-jalan.edit', $sj->id) }}" class="flex-1 border border-indigo-600 text-indigo-600 rounded text-center py-2">Edit</a>

                                <form action="{{ route('surat-jalan.destroy', $sj->id) }}" method="POST" class="flex-1" onclick="event.stopPropagation();" data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full border border-red-600 text-red-600 rounded text-center py-2">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- INFO + PAGINATION (CENTER) --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center w-full"></div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {

        let dataTable = null;
        const cards = [...document.querySelectorAll('.mobile-card')];
        const info = document.getElementById('mobileInfo');
        const pagination = document.getElementById('mobilePagination');
        const perPageSelect = document.getElementById('mobilePerPage');

        let perPage = parseInt(perPageSelect.value);
        let currentPage = 1;

        function renderMobile() {
            const total = cards.length;
            const pages = Math.ceil(total / perPage);
            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            cards.forEach((c, i) => c.style.display = i >= start && i < end ? 'block' : 'none');
            info.textContent = `Showing ${start+1} to ${Math.min(end,total)} of ${total} entries`;
            renderPagination(pages);
        }

        function renderPagination(totalPages) {
            pagination.innerHTML = '';

            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);

            const createBtn = (label, disabled, active, cb) => {
                const btn = document.createElement('button');
                btn.textContent = label;
                btn.disabled = disabled;
                btn.className = `
                px-3 py-1 text-sm rounded-md border
                ${active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'}
                ${disabled?'opacity-50 cursor-not-allowed':''}
            `;
                btn.onclick = cb;
                return btn;
            };

            pagination.appendChild(createBtn('Prev', currentPage === 1, false, () => {
                currentPage--;
                renderMobile();
            }));

            for (let i = startPage; i <= endPage; i++) {
                pagination.appendChild(createBtn(i, false, i === currentPage, () => {
                    currentPage = i;
                    renderMobile();
                }));
            }

            pagination.appendChild(createBtn('Next', currentPage === totalPages, false, () => {
                currentPage++;
                renderMobile();
            }));
        }

        perPageSelect.onchange = () => {
            perPage = parseInt(perPageSelect.value);
            currentPage = 1;
            renderMobile();
        };

        function handleResponsive() {
            if (window.innerWidth >= 1024) {
                if (!dataTable) {
                    dataTable = new DataTable('#dataTablesDesktop', {
                        responsive: true
                    });
                }
            } else {
                if (dataTable) {
                    dataTable.destroy();
                    dataTable = null;
                }
                renderMobile();
            }
        }

        handleResponsive();
        window.addEventListener('resize', handleResponsive);

        // Make mobile cards clickable
        cards.forEach(c => {
            c.addEventListener('click', () => {
                const href = c.getAttribute('data-href');
                if (href) window.location.href = href;
            });
        });

        // Desktop table clickable row
        document.querySelector('#dataTablesDesktop tbody')?.addEventListener('click', function(e) {
            const tr = e.target.closest('tr[data-href]');
            if (!tr) return;
            const href = tr.getAttribute('data-href');
            if (!href) return;
            window.location.href = href;
        });

    });
</script>
@endpush
