@extends('layouts.app')

@section('title', __('Setor Penjualan'))


@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Setor Penjualan') }}</h2>
@endsection


@section('content')
    <div class="py-2 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ================= SUMMARY ================= --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Total Belum Disetor --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="text-sm text-yellow-700 mb-1">Total Belum Disetor</div>
                    <div class="text-3xl font-extrabold text-yellow-800">
                        @php $amt = (int) ($notDepositedTotal ?? 0); @endphp
                        Rp {{ number_format($amt, 0, ',', '.') }}
                    </div>
                    @if (($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="mt-2 text-xs text-yellow-700">
                            Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}
                        </div>
                    @endif
                </div>

                {{-- Total Sudah Disetor --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="text-sm text-green-700 mb-1">Total Sudah Disetor</div>
                    <div class="text-3xl font-extrabold text-green-800">
                        @php $amtDeposited = (int) ($depositedTotal ?? 0); @endphp
                        Rp {{ number_format($amtDeposited, 0, ',', '.') }}
                    </div>
                    @if (($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="mt-2 text-xs text-green-700">
                            Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}
                        </div>
                    @endif
                </div>

                {{-- Filter --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <form method="GET" action="{{ route('invoices.setor') }}" class="grid gap-4">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="text-sm font-medium">Dari</label>
                                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}"
                                    class="w-full px-3 py-2.5 border rounded-lg">
                            </div>
                            <div>
                                <label class="text-sm font-medium">Sampai</label>
                                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}"
                                    class="w-full px-3 py-2.5 border rounded-lg">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                            <a href="{{ route('invoices.setor') }}"
                                class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= MAIN CARD ================= --}}
            <div class="bg-white shadow sm:rounded-lg w-full">
                <div class="p-4 sm:p-6 lg:p-8">

                    <h3 class="text-lg font-semibold mb-4">Daftar Invoice</h3>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-50 text-green-700 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- ================= DESKTOP TABLE ================= --}}
                    <div class="hidden lg:block w-full overflow-x-auto">
                        <table id="dataTablesSetor" class="min-w-full border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-3 py-2 text-xs">No</th>
                                    <th class="border px-3 py-2 text-xs">Invoice</th>
                                    <th class="border px-3 py-2 text-xs">Pelanggan</th>
                                    <th class="border px-3 py-2 text-xs">Tanggal</th>
                                    <th class="border px-3 py-2 text-xs">Total</th>
                                    <th class="border px-3 py-2 text-xs">Status</th>
                                    <th class="border px-3 py-2 text-xs">Bukti</th>
                                    <th class="border px-3 py-2 text-xs">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $inv)
                                    @php $isSetor = ($inv->status_setor ?? 'belum') === 'sudah'; @endphp
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                        <td class="border px-3 py-2 font-semibold">{{ $inv->invoice_number }}</td>
                                        <td class="border px-3 py-2">{{ $inv->customer->nama_customer ?? '-' }}</td>
                                        <td class="border px-3 py-2">{{ $inv->tanggal_invoice }}</td>
                                        <td class="border px-3 py-2">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}
                                        </td>
                                        <td class="border px-3 py-2">
                                            <span
                                                class="px-2 py-1 text-xs rounded
            {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isSetor ? 'Sudah' : 'Belum' }}
                                            </span>
                                        </td>
                                        <td class="border px-3 py-2">
                                            {!! $inv->bukti_setor
                                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="' .
                                                    asset('storage/' . $inv->bukti_setor) .
                                                    '">Lihat</a>'
                                                : '-' !!}
                                        </td>
                                        <td class="border px-3 py-2">
                                            <a href="{{ route('invoices.setor.edit', $inv) }}"
                                                class="text-blue-600 hover:text-blue-800 hover:underline">
                                                {{ $isSetor ? 'Edit Setor' : 'Setor' }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ================= MOBILE CARD ================= --}}
                    <div class="block lg:hidden mt-4" id="mobileWrapper">

                        <div class="flex justify-between mb-3">
                            <div class="text-sm text-gray-600">
                                Show
                                <select id="mobilePerPage" class="mx-1 border rounded text-sm">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                </select>
                                entries
                            </div>
                        </div>

                        <div id="mobileCards" class="space-y-3">
                            @foreach ($invoices as $inv)
                                @php $isSetor = ($inv->status_setor ?? 'belum') === 'sudah'; @endphp
                                <div class="mobile-card border rounded-lg bg-white shadow">

                                    <div class="px-4 py-3 bg-gray-50 border-b">
                                        <div class="text-xs text-gray-500">Invoice</div>
                                        <div class="font-semibold">{{ $inv->invoice_number }}</div>
                                        <div class="text-sm">{{ $inv->customer->nama_customer ?? '-' }}</div>
                                    </div>

                                    <div class="px-4 py-3 text-sm space-y-1">
                                        <div>Total: <b>Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</b></div>
                                        <div>Tanggal: {{ $inv->tanggal_invoice }}</div>
                                        <div>Status:
                                            <span
                                                class="px-2 py-0.5 rounded text-xs
            {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isSetor ? 'Sudah' : 'Belum' }}
                                            </span>
                                        </div>
                                        <div>Bukti: {!! $inv->bukti_setor
                                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="' .
                                                    asset('storage/' . $inv->bukti_setor) .
                                                    '">Lihat</a>'
                                                : '-' !!}</div>
                                    </div>

                                    <div class="px-4 py-3 bg-gray-50 border-t">
                                        <a href="{{ route('invoices.setor.edit', $inv) }}"
                                            class="block text-center border border-blue-600 text-blue-600 rounded py-2">
                                            {{ $isSetor ? 'Edit Setor' : 'Setor' }}
                                        </a>
                                    </div>

                                </div>
                            @endforeach
                        </div>

                        {{-- INFO + PAGINATION CENTER --}}
                        <div class="flex flex-col gap-3 mt-4">
                            <div id="mobileInfo" class="text-sm text-gray-600 text-center"></div>
                            <div class="flex justify-center w-full">
                                <div id="mobilePagination" class="flex gap-1"></div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                let dataTable = null;
                const cards = [...document.querySelectorAll('.mobile-card')];
                const info = document.getElementById('mobileInfo');
                const pagination = document.getElementById('mobilePagination');
                const perPageSelect = document.getElementById('mobilePerPage');
                const mobileWrapper = document.getElementById('mobileWrapper');

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

                function renderPagination(pages) {
                    pagination.innerHTML = '';
                    let s = Math.max(1, currentPage - 2);
                    let e = Math.min(pages, s + 4);

                    const btn = (t, d, a, cb) => {
                        const b = document.createElement('button');
                        b.textContent = t;
                        b.disabled = d;
                        b.className = `px-3 py-1 text-sm rounded border
            ${a?'bg-blue-600 text-white':'bg-white hover:bg-gray-100'}
            ${d?'opacity-50':''}`;
                        b.onclick = cb;
                        return b;
                    };

                    pagination.appendChild(btn('Prev', currentPage === 1, false, () => {
                        currentPage--;
                        renderMobile();
                    }));
                    for (let i = s; i <= e; i++) {
                        pagination.appendChild(btn(i, false, i === currentPage, () => {
                            currentPage = i;
                            renderMobile();
                        }));
                    }
                    pagination.appendChild(btn('Next', currentPage === pages, false, () => {
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
                        mobileWrapper.style.display = 'none';
                        if (!dataTable) {
                            dataTable = new DataTable('#dataTablesSetor', {
                                responsive: true
                            });
                        }
                    } else {
                        mobileWrapper.style.display = 'block';
                        if (dataTable) {
                            dataTable.destroy();
                            dataTable = null;
                        }
                        renderMobile();
                    }
                }

                handleResponsive();
                window.addEventListener('resize', handleResponsive);

            });
        </script>
    @endpush
