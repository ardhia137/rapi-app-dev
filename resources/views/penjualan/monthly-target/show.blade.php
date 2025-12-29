@extends('layouts.app')

@section('title', __('Detail Target Bulanan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Target Bulanan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Informasi Target</div>
                <div class="mt-2 text-xs text-gray-600 space-y-1">
                    <div>Nama: <span class="font-semibold">{{ $monthlyTarget->name }}</span></div>
                    <div>Periode: <span class="font-semibold">{{ \Carbon\Carbon::parse($monthlyTarget->start_date)->toDateString() }} s/d {{ \Carbon\Carbon::parse($monthlyTarget->end_date)->toDateString() }}</span></div>
                    <div>Target: <span class="font-semibold">Rp {{ number_format($monthlyTarget->target_amount ?? 0, 0, ',', '.') }}</span></div>
                    <div>Sisa Menuju Target: <span class="font-semibold">Rp {{ number_format(max(0, ($monthlyTarget->target_amount ?? 0) - ($actuals ?? 0)), 0, ',', '.') }}</span></div>
                    <div>Status: 
                        @php $status = $monthlyTarget->status; @endphp
                        @if ($status === 'achieved')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Tercapai</span>
                        @elseif ($status === 'ongoing')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Berjalan</span>
                        @elseif ($status === 'missed')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Tidak Tercapai</span>
                        @else
                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                        @endif
                    </div>
                    @if(!empty($monthlyTarget->notes))
                        <div>Catatan: <span class="font-semibold">{{ $monthlyTarget->notes }}</span></div>
                    @endif
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="text-sm text-gray-700 mb-1">Ringkasan</div>
                <div class="text-2xl font-bold text-gray-900">Rp {{ number_format(($actuals ?? 0), 0, ',', '.') }}</div>
                <div class="mt-2 text-xs text-gray-600">Total penjualan lunas (grand total invoice berstatus paid) dalam periode ini.</div>
            </div>
        </div>

        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <h3 class="mb-4">{{ __('Invoice dalam Periode') }}</h3>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full border border-gray-300" id="dataTablesInvoices">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Nomor</th>
                                <th class="px-4 py-2 border">Pelanggan</th>
                                <th class="px-4 py-2 border">Tanggal</th>
                                <th class="px-4 py-2 border">Grand Total</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $inv->invoice_number ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->customer->nama_customer ?? '-' }}</td>
                                    <td class="px-4 py-2 border">{{ $inv->tanggal_invoice }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($inv->grand_total ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">
                                        @php $status = $inv->status_pembayaran; @endphp
                                        @if ($status === 'paid')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                                        @elseif ($status === 'unpaid')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                                        @elseif ($status === 'overdue')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Terlambat</span>
                                        @elseif ($status === 'cancelled')
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                                        @else
                                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <a href="{{ route('invoices.show', $inv->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-2 border text-center text-gray-500">
                                        Belum ada invoice dalam periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden w-full" id="mobileCardWrapper">

                    {{-- TOP --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage" class="mx-1 border-gray-300 rounded-md text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            entries
                        </div>
                    </div>

                    {{-- CARDS --}}
                    <div id="mobileCards" class="space-y-3">
                        @forelse($invoices as $inv)
                        <div class="mobile-card bg-white border rounded-lg shadow-sm">
                            
                            <div class="px-4 py-3 bg-gray-50 border-b space-y-2">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $inv->invoice_number ?? '-' }}
                                    </h4>
                                    @php $status = $inv->status_pembayaran; @endphp
                                    @if ($status === 'paid')
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Lunas</span>
                                    @elseif ($status === 'unpaid')
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Lunas</span>
                                    @elseif ($status === 'overdue')
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Terlambat</span>
                                    @elseif ($status === 'cancelled')
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                                    @else
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                                    @endif
                                </div>

                                <div class="text-sm text-gray-600">
                                    {{ $inv->customer->nama_customer ?? '-' }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $inv->tanggal_invoice }}
                                </div>
                            </div>

                            <div class="px-4 py-3 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Grand Total:</span>
                                    <span class="font-semibold">
                                        Rp {{ number_format($inv->grand_total ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-4 py-3 border-t">
                                <a href="{{ route('invoices.show', $inv->id) }}"
                                   class="block w-full text-center px-3 py-2 border border-blue-600 rounded text-blue-600">
                                    Detail
                                </a>
                            </div>

                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            Belum ada invoice dalam periode ini
                        </div>
                        @endforelse
                    </div>

                    {{-- INFO + PAGINATION --}}
                    @if(count($invoices) > 0)
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                        <div id="mobileInfo" class="text-sm text-gray-600"></div>
                        <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
                    </div>
                    @endif
                </div>

                {{-- Laravel Pagination (Hidden on Mobile) --}}
                <div class="mt-4 hidden lg:block">
                    {{ $invoices->links() }}
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
                dataTable=new DataTable('#dataTablesTargets',{responsive:true});
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
