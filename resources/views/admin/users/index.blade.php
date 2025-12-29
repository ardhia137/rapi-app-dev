@extends('layouts.app')

@section('title', __('Pengguna'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Pengguna') }}</h2>
@endsection

@section('content')
    <div class="py-2 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- =================== TABEL PENGGUNA =================== --}}
            <div class="bg-white shadow sm:rounded-lg w-full mb-6">
                <div class="p-4 sm:p-6 lg:p-8">

                    {{-- HEADER --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between mb-6 gap-3">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Pengguna') }}</h3>

                        @can('users.create')
                            <a href="{{ route('users.create') }}"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                + Tambah Pengguna
                            </a>
                        @endcan
                    </div>

                    {{-- ALERT --}}
                    @if (session('success'))
                        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- ================= DESKTOP TABLE ================= --}}
                    <div class="hidden lg:block w-full overflow-x-auto">
                        <table id="usersTable" class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-black-500 uppercase border-r">
                                        No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r">
                                        Nama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r">
                                        Email</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r">
                                        Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r">
                                        Peran</th>
                                    @canany(['users.update', 'users.delete'])
                                        <th class="px-4 py-3 text-center text-xs font-medium text-black-500 uppercase">Aksi</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-center border-r">
                                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                        </td>
                                        <td class="px-4 py-3 border-r font-medium">{{ $user->name }}</td>
                                        <td class="px-4 py-3 border-r">{{ $user->email }}</td>
                                        <td class="px-4 py-3 border-r">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full
                                        {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 border-r">
                                            {{ $user->roles->pluck('name')->join(', ') ?: '-' }}
                                        </td>
                                        @canany(['users.update', 'users.delete'])
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center gap-3">
                                                    @can('users.update')
                                                        <a href="{{ route('users.edit', $user) }}"
                                                            class="text-blue-600 hover:underline">
                                                            Edit
                                                        </a>
                                                    @endcan
                                                    @can('users.delete')
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            data-confirm-delete>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:underline">
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

                        <div id="mobileCards" class="space-y-3">
                            @foreach ($users as $user)
                                <div class="mobile-card bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="px-4 py-3 bg-gray-50 border-b">
                                        <div class="text-xs text-gray-500">
                                            No. {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                        </div>
                                        <h4 class="text-base font-semibold text-gray-900">
                                            {{ $user->name }}
                                        </h4>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $user->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </div>

                                    <div class="px-4 py-3 text-sm space-y-1">
                                        <div><span class="text-gray-500">Email:</span> {{ $user->email }}</div>
                                        <div><span class="text-gray-500">Peran:</span>
                                            {{ $user->roles->pluck('name')->join(', ') ?: '-' }}</div>
                                    </div>

                                    @canany(['users.update', 'users.delete'])
                                        <div class="px-4 py-3 bg-gray-50 border-t flex gap-2">
                                            @can('users.update')
                                                <a href="{{ route('users.edit', $user) }}"
                                                    class="flex-1 text-center px-3 py-2 border border-blue-600 text-blue-600 rounded-md text-sm hover:bg-blue-50">
                                                    Edit
                                                </a>
                                            @endcan
                                            @can('users.delete')
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1"
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

                        <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                            <div id="mobileInfo" class="text-sm text-gray-600"></div>
                            <div id="mobilePagination" class="flex gap-1 flex-wrap justify-center"></div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- =================== TABEL ACTIVITY LOGS (Super Admin Only) =================== --}}
            {{-- COPY BAGIAN INI, REPLACE DARI BARIS 174 SAMPAI 249 DI FILE index.blade.php --}}

            @role('super-admin')
                <div class="bg-white shadow sm:rounded-lg w-full">
                    <div class="p-4 sm:p-6 lg:p-8">

                        {{-- HEADER --}}
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Activity Logs</h3>
                                <p class="text-sm text-gray-500 mt-1">Riwayat aktivitas semua user di sistem</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="activityCategoryFilter" class="text-sm text-gray-700">Kategori:</label>
                                <select id="activityCategoryFilter" class="border-gray-300 rounded-md text-sm">
                                    <option value="">Semua</option>
                                    @php
                                        $logCategories = collect($activityLogs)->pluck('category')->filter()->unique();
                                    @endphp
                                    @foreach ($logCategories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ================= DESKTOP TABLE ================= --}}
                        <div class="hidden lg:block w-full overflow-x-auto">
                            <table id="activityLogsTable" class="min-w-full border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-black-500 uppercase border-r"
                                            style="width: 4%;">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r"
                                            style="width: 13%;">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r"
                                            style="width: 13%;">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r"
                                            style="width: 10%;">Tipe</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase border-r"
                                            style="width: 39%;">Deskripsi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-black-500 uppercase"
                                            style="width: 11%;">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @forelse ($activityLogs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-center border-r text-sm">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="px-4 py-3 border-r">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $log->user->name ?? 'System' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $log->user->email ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 border-r">
                                                <div class="flex items-center gap-2 text-sm text-gray-800">
                                                    <span class="text-gray-600">{!! $log->category_icon_svg !!}</span>
                                                    <span>{{ $log->category ?? '-' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 border-r">
                                                <span class="px-2 py-1 text-xs rounded-full {{ $log->type_badge_color }}">
                                                    {{ $log->type_name }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 border-r text-sm text-gray-700">
                                                {{ $log->description }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                                Belum ada activity log
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- ================= MOBILE CARD ================= --}}
                        <div class="block lg:hidden w-full" id="mobileLogsWrapper">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm text-gray-600">
                                    Show
                                    <select id="mobileLogsPerPage" class="mx-1 border-gray-300 rounded-md text-sm">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                    </select>
                                    logs
                                </div>
                            </div>

                            <div id="mobileLogsCards" class="space-y-3">
                                @foreach ($activityLogs as $log)
                                    <div class="mobile-log-card bg-white border border-gray-200 rounded-lg shadow-sm">
                                        {{-- HEADER CARD --}}
                                        <div class="px-4 py-3 bg-gray-50 border-b">
                                            <div class="flex items-center justify-between">
                                                <div class="text-xs text-gray-500">
                                                    No. {{ $loop->iteration }}
                                                </div>
                                                <span class="px-2 py-1 text-xs rounded-full {{ $log->type_badge_color }}">
                                                    {{ $log->type_name }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-2">
                                                <span class="text-gray-600">{!! $log->category_icon_svg !!}</span>
                                                <h4 class="text-sm font-semibold text-gray-900">{{ $log->category ?? '-' }}
                                                </h4>
                                            </div>
                                        </div>

                                        {{-- CONTENT CARD --}}
                                        <div class="px-4 py-3 text-sm space-y-2">
                                            <div>
                                                <span class="text-gray-500 font-medium">User:</span>
                                                <div class="mt-1">
                                                    <div class="font-medium text-gray-900">{{ $log->user->name ?? 'System' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $log->user->email ?? '-' }}</div>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 font-medium">Deskripsi:</span>
                                                <div class="mt-1 text-gray-700">{{ $log->description }}</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-500 font-medium">Waktu:</span>
                                                <div class="mt-1">
                                                    <div class="text-gray-900">{{ $log->created_at->format('d/m/Y') }}</div>
                                                    <div class="text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- PAGINATION MOBILE --}}
                            <div class="flex flex-col sm:flex-row justify-between items-center mt-4 gap-3">
                                <div id="mobileLogsInfo" class="text-sm text-gray-600"></div>
                                <div id="mobileLogsPagination" class="flex gap-1 flex-wrap justify-center"></div>
                            </div>
                        </div>

                    </div>
                </div>
            @endrole

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ============= USERS TABLE (EXISTING) =============
            let usersTableInstance = null;
            const cards = Array.from(document.querySelectorAll('.mobile-card'));
            const pagination = document.getElementById('mobilePagination');
            const info = document.getElementById('mobileInfo');
            const perPageSelect = document.getElementById('mobilePerPage');
            const mobileWrapper = document.getElementById('mobileCardWrapper');

            let perPage = parseInt(perPageSelect?.value || 10);
            let currentPage = 1;

            function renderMobile() {
                if (!cards.length) return;

                const total = cards.length;
                const totalPages = Math.ceil(total / perPage);
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                cards.forEach((card, index) => {
                    card.style.display = index >= start && index < end ? 'block' : 'none';
                });

                if (info) {
                    info.textContent = `Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`;
                }

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {
                if (!pagination) return;

                pagination.innerHTML = '';

                const maxVisible = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
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
                ${active ? 'bg-blue-600 text-white border-blue-600'
                         : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
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

            if (perPageSelect) {
                perPageSelect.addEventListener('change', () => {
                    perPage = parseInt(perPageSelect.value);
                    currentPage = 1;
                    renderMobile();
                });
            }

            function handleResponsive() {
                if (window.innerWidth >= 1024) {
                    if (mobileWrapper) mobileWrapper.style.display = 'none';
                    if (!usersTableInstance && document.getElementById('usersTable')) {
                        usersTableInstance = new DataTable('#usersTable', {
                            responsive: true,
                            autoWidth: false,
                            pageLength: 10,
                            columnDefs: [{
                                orderable: false,
                                targets: -1
                            }],
                            language: {
                                search: "Cari:",
                                lengthMenu: "Tampilkan _MENU_ data",
                                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                                infoFiltered: "(difilter dari _MAX_ total data)",
                                paginate: {
                                    first: "Pertama",
                                    last: "Terakhir",
                                    next: "Selanjutnya",
                                    previous: "Sebelumnya"
                                }
                            }
                        });
                    }
                } else {
                    if (mobileWrapper) mobileWrapper.style.display = 'block';
                    if (usersTableInstance) {
                        usersTableInstance.destroy();
                        usersTableInstance = null;
                    }
                    renderMobile();
                }
            }

            handleResponsive();
            window.addEventListener('resize', handleResponsive);

            // Shared category filter element for Logs (desktop + mobile)
            const categoryFilter = document.getElementById('activityCategoryFilter');

            // ============= ACTIVITY LOGS TABLE (DESKTOP) =============
            let activityLogsTable = null;
            if (document.getElementById('activityLogsTable')) {
                activityLogsTable = new DataTable('#activityLogsTable', {
                    responsive: true,
                    autoWidth: false,
                    pageLength: 25,
                    order: [
                        [5, 'desc']
                    ], // Sort by Waktu descending
                    columnDefs: [{
                            orderable: true,
                            targets: [0, 1, 2, 3, 5]
                        },
                        {
                            orderable: false,
                            targets: [4]
                        }
                    ],
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ logs",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ logs",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 logs",
                        infoFiltered: "(difilter dari _MAX_ total logs)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Belum ada activity log",
                        zeroRecords: "Tidak ada data yang cocok"
                    }
                });

                // Apply category filter to DataTable (substring, case-insensitive)
                if (categoryFilter) {
                    categoryFilter.addEventListener('change', () => {
                        const val = categoryFilter.value;
                        activityLogsTable.column(2).search(val, false, true).draw();
                        // Also refresh mobile view state when visible
                        logCurrentPage = 1;
                        renderMobileLogs();
                    });
                }
            }

            // ============= ACTIVITY LOGS MOBILE =============
            const logCards = Array.from(document.querySelectorAll('.mobile-log-card'));
            const logPagination = document.getElementById('mobileLogsPagination');
            const logInfo = document.getElementById('mobileLogsInfo');
            const logPerPageSelect = document.getElementById('mobileLogsPerPage');
            const mobileLogsWrapper = document.getElementById('mobileLogsWrapper');

            let logPerPage = parseInt(logPerPageSelect?.value || 10);
            let logCurrentPage = 1;

            function renderMobileLogs() {
                if (!logCards.length) return;

                // Filter by selected category (if any)
                const selectedCat = categoryFilter?.value || '';
                const filtered = selectedCat
                    ? logCards.filter(card => {
                        const catText = (card.querySelector('h4')?.textContent || '').trim();
                        return catText === selectedCat;
                    })
                    : logCards.slice();

                const total = filtered.length;
                const totalPages = Math.max(1, Math.ceil(total / logPerPage));
                const start = (logCurrentPage - 1) * logPerPage;
                const end = start + logPerPage;

                // Hide all first, then show only the filtered page
                logCards.forEach(card => { card.style.display = 'none'; });
                filtered.forEach((card, index) => {
                    card.style.display = index >= start && index < end ? 'block' : 'none';
                });

                if (logInfo) {
                    logInfo.textContent = total
                        ? `Showing ${start + 1} to ${Math.min(end, total)} of ${total} logs`
                        : 'Showing 0 to 0 of 0 logs';
                }

                renderLogsPagination(totalPages);
            }

            function renderLogsPagination(totalPages) {
                if (!logPagination) return;

                logPagination.innerHTML = '';

                const maxVisible = 5;
                let startPage = Math.max(1, logCurrentPage - Math.floor(maxVisible / 2));
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
                ${active ? 'bg-blue-600 text-white border-blue-600'
                         : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'}
                ${disabled ? 'opacity-50 cursor-not-allowed' : ''}
            `;
                    btn.onclick = cb;
                    return btn;
                };

                logPagination.appendChild(createBtn('Prev', logCurrentPage === 1, false, () => {
                    logCurrentPage--;
                    renderMobileLogs();
                }));

                for (let i = startPage; i <= endPage; i++) {
                    logPagination.appendChild(createBtn(i, false, i === logCurrentPage, () => {
                        logCurrentPage = i;
                        renderMobileLogs();
                    }));
                }

                logPagination.appendChild(createBtn('Next', logCurrentPage === totalPages, false, () => {
                    logCurrentPage++;
                    renderMobileLogs();
                }));
            }

            if (logPerPageSelect) {
                logPerPageSelect.addEventListener('change', () => {
                    logPerPage = parseInt(logPerPageSelect.value);
                    logCurrentPage = 1;
                    renderMobileLogs();
                });
            }

            if (categoryFilter) {
                categoryFilter.addEventListener('change', () => {
                    logCurrentPage = 1;
                    renderMobileLogs();
                });
            }

            function handleLogsResponsive() {
                if (window.innerWidth >= 1024) {
                    if (mobileLogsWrapper) mobileLogsWrapper.style.display = 'none';
                } else {
                    if (mobileLogsWrapper) mobileLogsWrapper.style.display = 'block';
                    renderMobileLogs();
                }
            }

            handleLogsResponsive();
            window.addEventListener('resize', handleLogsResponsive);
        });
    </script>
@endpush
