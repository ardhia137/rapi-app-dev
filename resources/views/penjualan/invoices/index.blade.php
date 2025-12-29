@extends('layouts.app')

@section('title', __('Penjualan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Penjualan') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- ================= ATAS / SUMMARY ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            {{-- Rasio Lunas --}}
            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="text-sm text-green-700 mb-1">Rasio Lunas / Transaksi</div>
                <div class="text-4xl font-extrabold text-green-800 tracking-tight">
                    {{ number_format($paidCount ?? 0) }}
                    <span class="text-green-600">/</span>
                    {{ number_format($totalCount ?? 0) }}
                </div>
                <div class="mt-2 text-xs text-green-700 space-y-0.5">
                    <div>Lunas: <b>{{ number_format($paidCount ?? 0) }}</b> transaksi</div>
                    <div>Total: <b>{{ number_format($totalCount ?? 0) }}</b> transaksi</div>
                    @if(($dateFrom ?? null) || ($dateTo ?? null))
                        <div class="text-[11px] text-green-600">
                            Rentang: {{ $dateFrom ?? '—' }} s/d {{ $dateTo ?? '—' }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Filter Tanggal --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <form method="GET" action="{{ route('invoices.index') }}" class="grid grid-cols-1 gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                        <button class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Filter
                        </button>
                        <a href="{{ route('invoices.index') }}"
                           class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

        </div>

        {{-- ================= CARD PEMASUKAN ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-700 mb-1">Total Pemasukan (Lunas)</div>
                <div class="text-2xl font-bold text-green-800">
                    Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-700 mb-1">Total Pemasukan (Sudah Disetor)</div>
                <div class="text-2xl font-bold text-blue-800">
                    Rp {{ number_format($totalSetor ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- ================= MAIN WRAPPER ================= --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER TABLE --}}
                <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
                    <h3 class="text-lg font-semibold">Penjualan</h3>
                    <a href="{{ route('invoices.create') }}"
                       class="inline-flex items-center justify-center
                              px-4 py-2.5 bg-blue-600 text-white
                              rounded-lg hover:bg-blue-700">
                        + Tambah Penjualan
                    </a>
                </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Invoice</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Produk</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Qty</th>
                                <th class="px-3 py-2 border text-left text-xs uppercase">Pembeli</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Total</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Status</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Bukti Pembayaran</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $i)
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-left font-semibold">{{ $i->invoice_number }}</td>
                                <td class="border px-3 py-2 text-left">
                                    {{ $i->items->pluck('product.nama_produk')->join(', ') }}
                                </td>
                                <td class="border px-3 py-2">{{ $i->items->sum('quantity') }}</td>
                                <td class="border px-3 py-2 text-left">{{ $i->customer->nama_customer ?? '-' }}</td>
                                <td class="border px-3 py-2">{{ $i->tanggal_invoice }}</td>
                                <td class="border px-3 py-2 text-right">
                                    Rp {{ number_format($i->grand_total,0,',','.') }}
                                </td>
                                <td class="border px-3 py-2">
                                    @if($i->status_pembayaran === 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                                    @elseif($i->status_pembayaran === 'unpaid')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                                    @elseif($i->status_pembayaran === 'overdue')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                                    @elseif($i->status_pembayaran === 'cancelled')
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($i->status_pembayaran) }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">
                                    {!! $i->bukti_setor
                                        ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$i->bukti_setor).'">Lihat</a>'
                                        : '-' !!}
                                </td>
                                <td class="border px-3 py-2">
                                    <div class="flex flex-col space-y-1">
                                        <a href="{{ route('invoices.show', $i) }}"
                                        class="text-indigo-600 hover:text-indigo-800 hover:underline">
                                        Detail
                                        </a>

                                        <a href="{{ route('invoices.edit', $i) }}"
                                        class="text-blue-600 hover:text-blue-800 hover:underline">
                                        Edit
                                        </a>

                                        <form action="{{ route('invoices.destroy', $i) }}" method="POST" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="text-red-600 hover:text-red-800 hover:underline text-left">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

        {{-- ================= MOBILE CARD ================= --}}
        <div class="block lg:hidden mt-4">

            <div class="flex justify-between mb-3">
                <div class="text-sm">
                    Show
                    <select id="mobilePerPage" class="border rounded text-sm mx-1">
                        <option value="5">5</option>
                        <option value="10">10</option>
                    </select>
                    entries
                </div>
            </div>

            <div id="mobileCards" class="space-y-3">
                @foreach($invoices as $i)
                <div class="mobile-card border rounded-lg bg-white shadow">

                    <div class="px-4 py-3 bg-gray-50 border-b">
                        <div class="text-xs text-gray-500">Invoice</div>
                        <div class="font-semibold">{{ $i->invoice_number }}</div>
                        <div class="text-sm text-gray-600">{{ $i->customer->nama_customer ?? '-' }}</div>
                    </div>

                    <div class="px-4 py-3 text-sm space-y-1">
                        <div>Produk: {{ $i->items->pluck('product.nama_produk')->join(', ') }}</div>
                        <div>Qty: {{ $i->items->sum('quantity') }}</div>
                        <div>Total: <b>Rp {{ number_format($i->grand_total,0,',','.') }}</b></div>
                        <div>Tanggal: {{ $i->tanggal_invoice }}</div>
                        <div>Status: 
                            @if($i->status_pembayaran === 'paid')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Lunas</span>
                            @elseif($i->status_pembayaran === 'unpaid')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Belum Lunas</span>
                            @elseif($i->status_pembayaran === 'overdue')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Terlambat</span>
                            @elseif($i->status_pembayaran === 'cancelled')
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Batal</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">{{ ucfirst($i->status_pembayaran) }}</span>
                            @endif    
                        </div>
                        <div>Bukti Pembayaran: 
                            {!! $i->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$i->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                        <a href="{{ route('invoices.show', $i) }}"
                           class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2">
                            Detail
                        </a>
                        <a href="{{ route('invoices.edit',$i) }}"
                           class="flex-1 border border-indigo-600 text-indigo-600 rounded text-center py-2">
                            Edit
                        </a>
                        <form action="{{ route('invoices.destroy',$i) }}" method="POST" class="flex-1"
                            data-confirm-delete>
                            @csrf @method('DELETE')
                            <button class="w-full border border-red-600 text-red-600 rounded py-2">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
                @endforeach
            </div>

           <div class="flex flex-col gap-3 mt-4">
    <div id="mobileInfo" class="text-sm text-gray-600 text-center"></div>

    <div class="flex justify-center w-full">
        <div id="mobilePagination" class="flex gap-1"></div>
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

    function renderPagination(pages){
        pagination.innerHTML='';
        const max=5;
        let s=Math.max(1,currentPage-2);
        let e=Math.min(pages,s+max-1);

        const btn=(t,d,a,cb)=>{
            const b=document.createElement('button');
            b.textContent=t;
            b.disabled=d;
            b.className=`px-3 py-1 text-sm border rounded ${a?'bg-blue-600 text-white':''}`;
            b.onclick=cb;
            return b;
        };

        pagination.appendChild(btn('Prev',currentPage===1,false,()=>{currentPage--;renderMobile();}));
        for(let i=s;i<=e;i++){
            pagination.appendChild(btn(i,false,i===currentPage,()=>{currentPage=i;renderMobile();}));
        }
        pagination.appendChild(btn('Next',currentPage===pages,false,()=>{currentPage++;renderMobile();}));
    }

    perPageSelect.onchange=()=>{perPage=parseInt(perPageSelect.value);currentPage=1;renderMobile();};

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
});
</script>
@endpush
