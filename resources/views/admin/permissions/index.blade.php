@extends('layouts.app')

@section('title', __('Izin'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Izin') }}</h2>
@endsection

@section('content')
<div class="py-2 w-full">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg w-full">
            <div class="p-4 sm:p-6 lg:p-8">

                {{-- HEADER --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-6 gap-3">
                    <h3 class="text-lg font-semibold text-gray-900">Izin</h3>

                    @can('permissions.create')
                    <a href="{{ route('permissions.create') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        + Tambah Izin
                    </a>
                    @endcan
                </div>

                {{-- ALERT --}}
                @if(session('status'))
                    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- ================= DESKTOP TABLE ================= --}}
                <div class="hidden lg:block w-full overflow-x-auto">
                    <table id="dataTables" class="min-w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-black-500 uppercase border-r">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r">Nama</th>
                                @canany(['permissions.update','permissions.delete'])
                                <th class="px-4 py-3 text-center text-xs font-medium text-black-500 uppercase">Aksi</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($permissions as $permission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center border-r">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 border-r font-medium">{{ $permission->name }}</td>
                                @canany(['permissions.update','permissions.delete'])
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-3">
                                        @can('permissions.update')
                                        <a href="{{ route('permissions.edit', $permission) }}"
                                           class="text-blue-600 hover:underline">Edit</a>
                                        @endcan
                                        @can('permissions.delete')
                                        <form action="{{ route('permissions.destroy', $permission) }}"
                                              method="POST"
                                              data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ================= MOBILE CARD ================= --}}
                <div class="block lg:hidden w-full" id="mobileCardWrapper">

                    {{-- TOP CONTROL --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="text-sm text-gray-600">
                            Show
                            <select id="mobilePerPage"
                                    class="mx-1 border-gray-300 rounded-md text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                            entries
                        </div>
                    </div>

                    {{-- CARDS --}}
                    <div id="mobileCards" class="space-y-3">
                        @foreach ($permissions as $permission)
                        <div class="mobile-card bg-white border border-gray-200 rounded-lg shadow-sm">

                            <div class="px-4 py-3 bg-gray-50 border-b">
                                <div class="text-xs text-gray-500">No. {{ $loop->iteration }}</div>
                                <h4 class="text-base font-semibold text-gray-900">
                                    {{ $permission->name }}
                                </h4>
                            </div>

                            @canany(['permissions.update','permissions.delete'])
                            <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                @can('permissions.update')
                                <a href="{{ route('permissions.edit', $permission) }}"
                                   class="flex-1 text-center px-3 py-2 border border-blue-600 text-blue-600 rounded-md text-sm hover:bg-blue-50">
                                    Edit
                                </a>
                                @endcan
                                @can('permissions.delete')
                                <form action="{{ route('permissions.destroy', $permission) }}"
                                      method="POST"
                                      class="flex-1"
                                      data-confirm-delete>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-3 py-2 border border-red-600 text-red-600 rounded-md text-sm hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                                @endcan
                            </div>
                            @endcanany

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
document.addEventListener('DOMContentLoaded', function () {

    const table = $('#dataTables');
    const mobileWrapper = document.getElementById('mobileCardWrapper');

    let dataTableInstance = null;

    /* ================= MOBILE PAGINATION ================= */
    const cards = Array.from(document.querySelectorAll('.mobile-card'));
    const pagination = document.getElementById('mobilePagination');
    const info = document.getElementById('mobileInfo');
    const perPageSelect = document.getElementById('mobilePerPage');

    let perPage = parseInt(perPageSelect.value);
    let currentPage = 1;

    function renderMobile() {
        const total = cards.length;
        const totalPages = Math.ceil(total / perPage);
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        cards.forEach((card, index) => {
            card.style.display =
                index >= start && index < end ? 'block' : 'none';
        });

        info.textContent =
            `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;

        renderPagination(totalPages);
    }

    /* ================= PAGINATION (MAX 5 PAGE) ================= */
    function renderPagination(totalPages) {
        pagination.innerHTML = '';

        const maxVisible = 5;
        let startPage = Math.max(
            1,
            currentPage - Math.floor(maxVisible / 2)
        );
        let endPage = startPage + maxVisible - 1;

        if (endPage > totalPages) {
            endPage = totalPages;
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        const createBtn = (label, disabled, active, cb) => {
            const btn = document.createElement('button');
            btn.textContent = label;
            btn.disabled = disabled;
            btn.className = `
                px-3 py-1 text-sm rounded-md border
                ${active
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
            `;
            btn.onclick = cb;
            return btn;
        };

        // Prev
        pagination.appendChild(
            createBtn('Prev', currentPage === 1, false, () => {
                currentPage--;
                renderMobile();
            })
        );

        // Page numbers (max 5)
        for (let i = startPage; i <= endPage; i++) {
            pagination.appendChild(
                createBtn(i, false, i === currentPage, () => {
                    currentPage = i;
                    renderMobile();
                })
            );
        }

        // Next
        pagination.appendChild(
            createBtn('Next', currentPage === totalPages, false, () => {
                currentPage++;
                renderMobile();
            })
        );
    }

    perPageSelect.addEventListener('change', () => {
        perPage = parseInt(perPageSelect.value);
        currentPage = 1;
        renderMobile();
    });

    /* ================= SWITCH MODE ================= */
    function handleResponsive() {
        if (window.innerWidth >= 1024) {
            // DESKTOP
            mobileWrapper.style.display = 'none';

            if (!dataTableInstance) {
                dataTableInstance = new DataTable('#dataTables', {
                    responsive: true,
                    autoWidth: false,
                    columnDefs: [{ orderable: false, targets: -1 }]
                });
            }
        } else {
            // MOBILE
            mobileWrapper.style.display = 'block';

            if (dataTableInstance) {
                dataTableInstance.destroy();
                dataTableInstance = null;
            }

            renderMobile();
        }
    }

    // INIT
    handleResponsive();

    // RESIZE
    window.addEventListener('resize', handleResponsive);
});
</script>
@endpush