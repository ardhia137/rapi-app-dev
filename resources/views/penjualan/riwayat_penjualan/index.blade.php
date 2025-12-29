@extends('layouts.app')

@section('title', __('Riwayat Transaksi'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Riwayat Transaksi') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- SUMMARY & FILTER CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Left: Summary -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Ringkasan</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format(($riwayat->count() ?? 0), 0, ',', '.') }} transaksi</div>
                @php
                    $countInv = $riwayat->where('type','invoice')->count();
                    $countSJ = $riwayat->where('type','surat_jalan')->count();
                @endphp
                <div class="mt-2 text-xs text-gray-600 space-y-0.5">
                    <div>Invoice: <span class="font-semibold">{{ number_format($countInv, 0, ',', '.') }}</span></div>
                    <div>Surat Jalan: <span class="font-semibold">{{ number_format($countSJ, 0, ',', '.') }}</span></div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="text-[11px] text-gray-500">Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}</div>
                    @endif
                </div>
            </div>

            <!-- Right: Filter -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('riwayat-penjualan.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-3">
    <div class="flex flex-col">
        <label class="block text-sm font-small text-gray-700 mb-1">Tipe</label>
        <select name="filter" class="w-full px-3 py-2.5 border rounded-lg h-10 box-border">
            <option value="" {{ ($filter ?? '')=='' ? 'selected' : '' }}>Semua</option>
            <option value="invoice" {{ ($filter ?? '')=='invoice' ? 'selected' : '' }}>Invoice</option>
            <option value="surat_jalan" {{ ($filter ?? '')=='surat_jalan' ? 'selected' : '' }}>Surat Jalan</option>
        </select>
    </div>
    <div class="flex flex-col">
        <label class="block text-sm font-small text-gray-700 mb-1">Dari </label>
        <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg h-10 box-border">
    </div>
    <div class="flex flex-col">
        <label class="block text-sm font-small text-gray-700 mb-1">Sampai </label>
        <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="w-full px-3 py-2.5 border rounded-lg h-10 box-border">
    </div>
</div>

                    <div class="flex gap-2 justify-end">
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                        <a href="{{ route('riwayat-penjualan.index') }}" class="px-4 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- MAIN WRAPPER --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER WITH EXPORT --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">{{ __('Riwayat Transaksi') }}</h3>
                    <a href="{{ route('riwayat-penjualan.pdf', request()->query()) }}"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex items-center justify-center
                              px-4 py-2.5 bg-red-600 text-white
                              rounded-lg hover:bg-red-700">
                        Export PDF
                    </a>
                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTablesRiwayat" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Jenis</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Nomor</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Pelanggan</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Grand Total</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $i => $row)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center text-sm">{{ $i + 1 }}</td>
                                <td class="border px-3 py-2">
                                    <span class="font-medium">{{ $row['type'] === 'invoice' ? 'Invoice' : 'Surat Jalan' }}</span>
                                </td>
                                <td class="border px-3 py-2 font-semibold">{{ $row['nomor'] ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $row['customer'] ?? '-' }}</td>
                                <td class="border px-3 py-2 text-center text-sm">{{ $row['tanggal'] ?? '-' }}</td>
                                <td class="border px-3 py-2 text-right">
                                    Rp {{ number_format($row['grand_total'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    @php $status = $row['status'] ?? null; @endphp
                                    @if (($row['type'] ?? '') === 'invoice')
                                        @if ($status === 'paid')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                        @elseif ($status === 'unpaid')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                        @elseif ($status === 'overdue')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                        @elseif ($status === 'cancelled')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    @else
                                        @if ($status === 'sudah dikirim')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Sudah Dikirim</span>
                                        @elseif ($status === 'pending')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Dikirim</span>
                                        @elseif ($status === 'cancel')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <a href="{{ $row['link'] }}" class="text-blue-600 hover:underline">Detail</a>
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
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                            </select>
                            entries
                        </div>
                    </div>

                    {{-- CARDS --}}
                    <div id="mobileCards" class="space-y-3">
                        @foreach($riwayat as $i => $row)
                        <div class="mobile-card border rounded-lg bg-white shadow">

                            {{-- HEADER --}}
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $row['nomor'] ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $row['type'] === 'invoice' ? ' Invoice' : '📋 Surat Jalan' }}
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $row['tanggal'] ?? '-' }}
                                    </div>
                                </div>
                            </div>

                            {{-- CONTENT --}}
                            <div class="px-4 py-3 text-sm space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Pelanggan:</span>
                                    <span class="font-medium text-gray-900">{{ $row['customer'] ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Grand Total:</span>
                                    <span class="font-bold text-gray-900">Rp {{ number_format($row['grand_total'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Status:</span>
                                    @php $status = $row['status'] ?? null; @endphp
                                    @if (($row['type'] ?? '') === 'invoice')
                                        @if ($status === 'paid')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                        @elseif ($status === 'unpaid')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                        @elseif ($status === 'overdue')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                        @elseif ($status === 'cancelled')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    @else
                                        @if ($status === 'sudah dikirim')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Sudah Dikirim</span>
                                        @elseif ($status === 'pending')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Dikirim</span>
                                        @elseif ($status === 'cancel')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Dibatalkan</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            {{-- FOOTER --}}
                            <div class="px-4 py-3 bg-gray-50 border-t">
                                <a href="{{ $row['link'] }}"
                                   class="block w-full border border-blue-600 text-blue-600 rounded text-center py-2 hover:bg-blue-50">
                                    Lihat Detail
                                </a>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- INFO + PAGINATION --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
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

    function renderMobile(){
        const total = cards.length;
        const pages = Math.ceil(total / perPage);
        const start = (currentPage-1)*perPage;
        const end = start + perPage;

        cards.forEach((c,i)=>c.style.display = i>=start && i<end ? 'block':'none');
        info.textContent = `Showing ${start+1} to ${Math.min(end,total)} of ${total} entries`;
        renderPagination(pages);
    }

    function renderPagination(totalPages){
        pagination.innerHTML='';

        const maxVisible=5;
        let startPage=Math.max(1,currentPage-2);
        let endPage=Math.min(totalPages,startPage+maxVisible-1);

        const createBtn=(label,disabled,active,cb)=>{
            const btn=document.createElement('button');
            btn.textContent=label;
            btn.disabled=disabled;
            btn.className=`
                px-3 py-1 text-sm rounded-md border
                ${active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'}
                ${disabled?'opacity-50 cursor-not-allowed':''}
            `;
            btn.onclick=cb;
            return btn;
        };

        pagination.appendChild(createBtn('Prev',currentPage===1,false,()=>{
            currentPage--; renderMobile();
        }));

        for(let i=startPage;i<=endPage;i++){
            pagination.appendChild(createBtn(i,false,i===currentPage,()=>{
                currentPage=i; renderMobile();
            }));
        }

        pagination.appendChild(createBtn('Next',currentPage===totalPages,false,()=>{
            currentPage++; renderMobile();
        }));
    }

    perPageSelect.onchange=()=>{
        perPage=parseInt(perPageSelect.value);
        currentPage=1;
        renderMobile();
    };

    function handleResponsive(){
        if(window.innerWidth>=1024){
            if(!dataTable){
                dataTable=new DataTable('#dataTablesRiwayat',{responsive:true});
            }
        }else{
            if(dataTable){
                dataTable.destroy();
                dataTable=null;
            }
            renderMobile();
        }
    }

    handleResponsive();
    window.addEventListener('resize',handleResponsive);
});
</script>
@endpush