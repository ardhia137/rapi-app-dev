@extends('layouts.app')

@section('title', __('Input Keuangan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Input Keuangan') }}</h2>
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
                <div class="text-xs text-purple-500 mt-2">Periode: {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}</div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-600 mb-1">Kas Kantor</div>
                <div class="text-xl text-gray-500">Belum ada anggaran untuk periode ini</div>
                <a href="{{ route('budget-target.create') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">+ Tambah Data Keuangan</a>
            </div>
            @endif

            <!-- Filter Periode Card -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('finance-records.index') }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Periode</label>
                    <select name="periode" class="w-full px-3 py-2.5 border rounded-lg" onchange="this.form.submit()">
                        @if($availablePeriods->isEmpty())
                            <option value="">Belum ada anggaran</option>
                        @else
                            @foreach($availablePeriods as $p)
                                <option value="{{ $p }}" {{ $periode == $p ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($p . '-01')->format('F Y') }}
                                    @if($p == date('Y-m')) (Bulan Ini) @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                </form>
            </div>
        </div>

        {{-- Row 2: Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @php
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
            <h3 class="text-lg font-semibold">{{ __('Input Keuangan') }} - {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}</h3>
            <a href="{{ route('finance-records.create') }}"
               class="inline-flex items-center justify-center
                      px-4 py-2.5 bg-blue-600 text-white
                      rounded-lg hover:bg-blue-700">
                + Tambah Data Keuangan
            </a>
        </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tipe</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Kategori</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Jumlah</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Foto Nota</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Deskripsi</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Dibuat Oleh</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($financeRecords as $fr)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center">{{ $loop->iteration }}</td>
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
                                        Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                    </span>
                               <td class="border px-3 py-2 text-center">
                                    {!! $fr->foto_nota
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$fr->foto_nota).'">Lihat</a>'
                                        : '-' !!}
                                </td>

                                
                                <td class="border px-3 py-2">{{ $fr->deskripsi ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $fr->user->name ?? 'Unknown' }}</td>
                                <td class="border px-3 py-2 text-center">
                                    <a href="{{ route('finance-records.edit', $fr->id) }}"
                                       class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('finance-records.destroy', $fr->id) }}"
                                          method="POST" class="inline" data-confirm-delete>
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline ms-3">Hapus</button>
                                    </form>
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
                        @foreach($financeRecords as $fr)
                        <div class="mobile-card border rounded-lg bg-white shadow">

                            {{-- HEADER --}}
                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $fr->kategori }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}
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
                                <div class="mb-2">
                                    <span class="text-gray-600 block mb-1">Foto Nota: {!! $fr->foto_nota
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$fr->foto_nota).'">Lihat</a>'
                                        : '-' !!}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Jumlah:</span>
                                    <span class="{{ $fr->tipe === 'income' ? 'text-green-600' : 'text-red-600' }} font-bold">
                                        Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
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

                            {{-- FOOTER --}}
                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                <a href="{{ route('finance-records.edit', $fr->id) }}"
                                   class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2 hover:bg-blue-50">
                                    Edit
                                </a>
                                <form action="{{ route('finance-records.destroy', $fr->id) }}"
                                      method="POST" class="flex-1" data-confirm-delete>
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-full border border-red-600 text-red-600 rounded text-center py-2 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- INFO + PAGINATION --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination"
                             class="flex gap-1 flex-wrap justify-center w-full">
                        </div>
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
                dataTable=new DataTable('#dataTables',{responsive:true});
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

    // Delete confirmation for desktop
    // document.querySelectorAll('[data-confirm-delete]').forEach(form => {
    //     form.onsubmit = e => {
    //         if(!confirm('Apakah Anda yakin ingin menghapus data ini?')) e.preventDefault();
    //     };
    // });

    // // Delete confirmation for mobile
    // document.querySelectorAll('[data-confirm-delete-mobile]').forEach(form => {
    //     form.onsubmit = e => {
    //         if(!confirm('Apakah Anda yakin ingin menghapus data ini?')) e.preventDefault();
    //     };
    // });
});

// SweetAlert2 for success message
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 2000
    });
@endif
</script>
@endpush
