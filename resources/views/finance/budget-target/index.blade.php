@extends('layouts.app')

@section('title', __('Target Anggaran'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Target Anggaran') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">

        {{-- HEADER --}}


        {{-- MAIN WRAPPER --}}
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">
                 <div class="flex flex-col sm:flex-row justify-between gap-3 mb-6">
            <h3 class="text-lg font-semibold">{{ __('Target Anggaran') }}</h3>
            <a href="{{ route('budget-target.create') }}"
               class="inline-flex items-center justify-center
                      px-4 py-2.5 bg-blue-600 text-white
                      rounded-lg hover:bg-blue-700">
                + Tambah Target Anggaran
            </a>
        </div>

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 border text-center text-xs uppercase">No</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Tanggal</th>
                                <th class="px-3 py-2 border text-right text-xs uppercase">Budget Bulanan</th>
                                <th class="px-3 py-2 border text-center text-xs uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($budgetTargets as $bt)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="border px-3 py-2 text-center">
                                    {{ \Carbon\Carbon::parse($bt->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="border px-3 py-2 text-right font-semibold">
                                    Rp {{ number_format($bt->budget_bulanan, 0, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    <a href="{{ route('budget-target.edit', $bt->id) }}"
                                       class="text-blue-600 hover:underline">Edit</a>
                                    <form action="{{ route('budget-target.destroy', $bt->id) }}"
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
                        @foreach($budgetTargets as $bt)
                        <div class="mobile-card border rounded-lg bg-white shadow">

                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="font-semibold text-gray-900">
                                    Budget Bulanan #{{ $loop->iteration }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($bt->tanggal)->format('d/m/Y') }}
                                </div>
                            </div>

                            <div class="px-4 py-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Budget:</span>
                                    <span class="font-bold text-gray-900">
                                        Rp {{ number_format($bt->budget_bulanan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                <a href="{{ route('budget-target.edit', $bt->id) }}"
                                   class="flex-1 border border-blue-600 text-blue-600 rounded text-center py-2 hover:bg-blue-50">
                                    Edit
                                </a>
                                <form action="{{ route('budget-target.destroy', $bt->id) }}"
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

    // Delete confirmation for mobile
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
