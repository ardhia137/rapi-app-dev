@extends('layouts.app')

@section('title', __('Riwayat Keuangan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Riwayat Keuangan') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- Row 1: Target Bulanan & Filter --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Target Bulanan Card -->
            @if($budgetTarget)
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="text-sm text-purple-600 mb-1">Kas Kantor</div>
                <div class="text-3xl font-bold text-purple-700">Rp {{ number_format($budgetTarget->budget_bulanan, 0, ',', '.') }}</div>
                <div class="text-xs text-purple-500 mt-2">
                    Periode: {{ $periode ? \Carbon\Carbon::parse($periode . '-01')->format('F Y') : 'Semua Periode' }}
                </div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Kas Kantor</div>
                <div class="text-xl text-gray-500">
                    {{ $periode ? 'Belum ada anggaran untuk periode ini' : 'Pilih periode untuk melihat anggaran' }}
                </div>
            </div>
            @endif

            <!-- Filter Card -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('finance-records.history') }}" id="filterForm">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter</label>
                    <div class="space-y-2">
                        <select name="periode" id="periodeSelect" class="w-full px-3 py-2.5 border rounded-lg text-sm">
                            @php
                                $currentMonth = date('Y-m');
                                $hasCurrentMonth = $availablePeriods->contains($currentMonth);
                            @endphp

                            @if($hasCurrentMonth)
                                <option value="{{ $currentMonth }}" {{ $periode == $currentMonth ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($currentMonth . '-01')->format('F Y') }} (Bulan Ini)
                                </option>
                            @endif

                            @foreach($availablePeriods->reject(fn($p) => $p == $currentMonth) as $p)
                                <option value="{{ $p }}" {{ $periode == $p ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($p . '-01')->format('F Y') }}
                                </option>
                            @endforeach

                            <option value="" {{ !$periode ? 'selected' : '' }}>Semua Periode</option>
                        </select>
                        <div class="text-center text-xs text-gray-500 py-1">atau</div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="date" name="start_date" id="startDate" value="{{ $startDate }}" placeholder="Dari" class="px-2 py-2 border rounded-lg text-sm">
                            <input type="date" name="end_date" id="endDate" value="{{ $endDate }}" placeholder="Sampai" class="px-2 py-2 border rounded-lg text-sm">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filter</button>
                            <a href="{{ route('finance-records.history') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Row 2: Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @php
                $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
                $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
                $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;
            @endphp

            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600 mb-1">Total Pengeluaran</div>
                <div class="text-2xl font-bold text-red-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 mb-1">Saldo Sisa</div>
                <div class="text-2xl font-bold {{ $saldoSisa >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                    Rp {{ number_format($saldoSisa, 0, ',', '.') }}
                </div>
                @if($budgetTarget)
                <div class="text-xs text-gray-500 mt-1">
                    {{ $saldoSisa >= 0 ? 'Tersisa' : 'Overbudget' }}
                    {{ number_format(abs(($saldoSisa / $budgetTarget->budget_bulanan) * 100), 1) }}%
                </div>
                @endif
            </div>
        </div>

        {{-- HEADER --}}


        {{-- MAIN WRAPPER --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
            <h3 class="text-lg font-semibold">
                {{ __('Riwayat Keuangan') }}
                @if($periode)
                    - {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}
                @elseif($startDate && $endDate)
                    - {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @endif
            </h3>

            <!-- Export Buttons -->
            <div class="flex flex-wrap gap-2">
                <!-- PDF Button -->
                @php
                    $pdfUrl = route('finance-records.preview-pdf');
                    if($periode) {
                        $pdfUrl .= '?periode=' . $periode;
                    } elseif($startDate && $endDate) {
                        $pdfUrl .= '?start_date=' . $startDate . '&end_date=' . $endDate;
                    }
                @endphp
                <a href="{{ $pdfUrl }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </a>

                <!-- Excel Button -->
                <button id="excelBtn"
                        class="inline-flex items-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Excel
                </button>

                <!-- Print Button -->
                <button id="printBtn"
                        class="inline-flex items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print
                </button>
            </div>
        </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Periode</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tipe</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Kategori</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Jumlah</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Deskripsi</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Dibuat Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financeRecords as $fr)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-center">
                                    {{ \Carbon\Carbon::parse($fr->periode . '-01')->format('M Y') }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    @if($fr->tipe === 'income')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Pemasukan</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Pengeluaran</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">{{ $fr->kategori }}</td>
                                <td class="border px-3 py-2 text-right">
                                    <span class="{{ $fr->tipe === 'income' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                                        {{ $fr->tipe === 'income' ? '+' : '-' }} Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="border px-3 py-2">{{ $fr->deskripsi ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $fr->user->name ?? 'Unknown' }}</td>
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
                        @foreach($financeRecords as $fr)
                        <div class="mobile-card border rounded-lg bg-white shadow">

                            {{-- HEADER --}}
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $fr->kategori }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }} • {{ \Carbon\Carbon::parse($fr->periode . '-01')->format('M Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        @if($fr->tipe === 'income')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Pemasukan</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Pengeluaran</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- CONTENT --}}
                            <div class="px-4 py-3 text-sm space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jumlah:</span>
                                    <span class="{{ $fr->tipe === 'income' ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        {{ $fr->tipe === 'income' ? '+' : '-' }} Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Deskripsi:</span>
                                    <span class="font-medium text-gray-900 text-right">{{ $fr->deskripsi ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dibuat Oleh:</span>
                                    <span class="font-medium text-gray-900">{{ $fr->user->name ?? 'Unknown' }}</span>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- INFO + PAGINATION --}}
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

    // ========== Filter Logic ==========
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const periodeSelect = document.getElementById('periodeSelect');

    startDate.addEventListener('change', function() {
        if (this.value) periodeSelect.value = '';
    });

    endDate.addEventListener('change', function() {
        if (this.value) periodeSelect.value = '';
    });

    periodeSelect.addEventListener('change', function() {
        if (this.value) {
            startDate.value = '';
            endDate.value = '';
        }
    });

    // ========== Mobile Pagination Logic ==========
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
                dataTable=new DataTable('#dataTables',{
                    responsive:true,


                });
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

    // ========== Excel Export ==========
    $('#excelBtn').on('click', function() {
        @php
            $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
            $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
            $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;
        @endphp

        var table = $('#dataTables').DataTable();
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: function() {
                        let title = 'Riwayat Keuangan';
                        @if($periode)
                            title += ' - {{ \Carbon\Carbon::parse($periode . "-01")->format("F Y") }}';
                        @endif
                        @if($startDate && $endDate)
                            title += ' ({{ \Carbon\Carbon::parse($startDate)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($endDate)->format("d/m/Y") }})';
                        @endif
                        return title;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        var lastRow = $('row', sheet).length;
                        lastRow++;

                        $('row:last', sheet).after(
                            '<row r="' + (lastRow + 2) + '">' +
                            '<c t="inlineStr" r="F' + (lastRow + 2) + '"><is><t>Total Pengeluaran:</t></is></c>' +
                            '<c t="inlineStr" r="G' + (lastRow + 2) + '"><is><t>Rp {{ number_format($totalPengeluaran, 0, ",", ".") }}</t></is></c>' +
                            '</row>'
                        );

                        @if($budgetTarget)
                        $('row:last', sheet).after(
                            '<row r="' + (lastRow + 3) + '">' +
                            '<c t="inlineStr" r="F' + (lastRow + 3) + '"><is><t>Kas Kantor:</t></is></c>' +
                            '<c t="inlineStr" r="G' + (lastRow + 3) + '"><is><t>Rp {{ number_format($budgetTarget->budget_bulanan, 0, ",", ".") }}</t></is></c>' +
                            '</row>'
                        );

                        $('row:last', sheet).after(
                            '<row r="' + (lastRow + 4) + '">' +
                            '<c t="inlineStr" r="F' + (lastRow + 4) + '"><is><t>Saldo Sisa:</t></is></c>' +
                            '<c t="inlineStr" r="G' + (lastRow + 4) + '"><is><t>Rp {{ number_format($saldoSisa, 0, ",", ".") }}</t></is></c>' +
                            '</row>'
                        );
                        @endif
                    }
                }
            ]
        });

        buttons.container().appendTo($('body'));
        $('.buttons-excel').click();
        buttons.destroy();
    });

    // ========== Print ==========
    $('#printBtn').on('click', function() {
        @php
            $totalPemasukan = $financeRecords->where('tipe', 'income')->sum('jumlah');
            $totalPengeluaran = $financeRecords->where('tipe', 'expense')->sum('jumlah');
            $saldoSisa = $budgetTarget ? ($budgetTarget->budget_bulanan - $totalPengeluaran) : 0;
        @endphp

        var table = $('#dataTables').DataTable();
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'print',
                    title: function() {
                        let title = 'Riwayat Keuangan';
                        @if($periode)
                            title += ' - {{ \Carbon\Carbon::parse($periode . "-01")->format("F Y") }}';
                        @endif
                        @if($startDate && $endDate)
                            title += '<br>({{ \Carbon\Carbon::parse($startDate)->format("d/m/Y") }} - {{ \Carbon\Carbon::parse($endDate)->format("d/m/Y") }})';
                        @endif
                        return title;
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
                    },
                    customize: function(win) {
                        $(win.document.body).append(
                            '<div style="margin-top: 30px; border-top: 2px solid #000; padding-top: 20px;">' +
                            '<h3 style="margin-bottom: 15px;">Ringkasan:</h3>' +
                            '<table style="width: 50%;">' +
                            '<tr><td><b>Total Pengeluaran:</b></td><td style="text-align: right; color: red;">Rp {{ number_format($totalPengeluaran, 0, ",", ".") }}</td></tr>' +
                            @if($budgetTarget)
                            '<tr><td><b>Kas Kantor:</b></td><td style="text-align: right; color: purple;">Rp {{ number_format($budgetTarget->budget_bulanan, 0, ",", ".") }}</td></tr>' +
                            '<tr><td><b>Saldo Sisa:</b></td><td style="text-align: right; color: {{ $saldoSisa >= 0 ? "blue" : "red" }};">Rp {{ number_format($saldoSisa, 0, ",", ".") }}</td></tr>' +
                            @endif
                            '</table>' +
                            '</div>'
                        );

                        $(win.document.body).find('table').addClass('display').css('font-size', '10pt');
                    }
                }
            ]
        });

        buttons.container().appendTo($('body'));
        $('.buttons-print').click();
        buttons.destroy();
    });
});
</script>
@endpush
