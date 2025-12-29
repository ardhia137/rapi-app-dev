@extends('layouts.app')

@section('title', 'Preview Laporan Keuangan PDF')

@section('header')
    <h2 class="text-xl font-semibold text-gray-800">Preview Laporan Keuangan</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Action Buttons -->
        <div class="mb-4 flex flex-wrap gap-3 justify-between items-center bg-white p-4 rounded-lg shadow">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    Laporan Keuangan
                    @if($periode)
                        - {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}
                    @elseif($startDate && $endDate)
                        - {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @endif
                </h3>
                <p class="text-sm text-gray-600 mt-1">Preview dokumen sebelum download</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('finance-records.history') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>

                @php
                    $downloadUrl = route('finance-records.download-pdf');
                    if($periode) {
                        $downloadUrl .= '?periode=' . $periode;
                    } elseif($startDate && $endDate) {
                        $downloadUrl .= '?start_date=' . $startDate . '&end_date=' . $endDate;
                    }
                @endphp
                <a href="{{ $downloadUrl }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>

        <!-- PDF Preview Container -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="bg-gray-100 rounded-lg overflow-hidden" style="min-height: 800px;">
                <!-- Preview Content menggunakan styling yang sama dengan PDF -->
                <div class="bg-white p-8 mx-auto" style="max-width: 800px;">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-8 pb-6 border-b-4 border-gray-800">
                        <div class="flex items-start">
                            <div class="mr-4">
                                <img src="{{ asset('images/logo-rapi.png') }}" alt="Logo" style="width: 120px; height: auto;">
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-800 mb-2">RAPI PVC</div>
                                <div class="text-xs text-gray-600 leading-relaxed">
                                    Jl. Alamat Perusahaan No. 123<br>
                                    Kota, Provinsi 12345<br>
                                    Telp: (021) 1234-5678<br>
                                    Email: info@perusahaan.com<br>
                                    Website: www.perusahaan.com
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-gray-800 mb-2">LAPORAN KEUANGAN</div>
                            <div class="text-sm text-gray-600">
                                <strong>Periode:</strong>
                                @if($periode)
                                    {{ \Carbon\Carbon::parse($periode . '-01')->format('F Y') }}
                                @elseif($startDate && $endDate)
                                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                                @else
                                    Semua Periode
                                @endif
                                <br>
                                <strong>Dicetak:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}<br>
                                <strong>Oleh:</strong> {{ Auth::user()->name ?? 'Admin' }}
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-purple-50 border-2 border-purple-400 rounded-lg p-4 text-center">
                            <div class="text-xs font-bold text-purple-700 mb-2 uppercase">Target Bulanan</div>
                            <div class="text-xl font-bold text-purple-800">
                                Rp {{ number_format($budgetTarget ? $budgetTarget->budget_bulanan : 0, 0, ',', '.') }}
                            </div>
                            @if(!$budgetTarget)
                            <div class="text-xs text-gray-500 mt-1">Belum ada target</div>
                            @endif
                        </div>
                        <div class="bg-red-50 border-2 border-red-400 rounded-lg p-4 text-center">
                            <div class="text-xs font-bold text-red-700 mb-2 uppercase">Total Pengeluaran</div>
                            <div class="text-xl font-bold text-red-800">
                                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="bg-blue-50 border-2 border-blue-400 rounded-lg p-4 text-center">
                            <div class="text-xs font-bold text-blue-700 mb-2 uppercase">Saldo Sisa</div>
                            <div class="text-2xl font-bold {{ $saldoSisa >= 0 ? 'text-blue-800' : 'text-red-800' }}">
                                Rp {{ number_format($saldoSisa, 0, ',', '.') }}
                            </div>
                            @if($budgetTarget)
                            <div class="text-xs text-gray-600 mt-1">
                                {{ $saldoSisa >= 0 ? 'Tersisa' : 'Overbudget' }}
                                {{ number_format(abs(($saldoSisa / $budgetTarget->budget_bulanan) * 100), 1) }}%
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-left" style="width: 5%;">No</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-left" style="width: 12%;">Tanggal</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-center" style="width: 13%;">Tipe</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-left" style="width: 15%;">Kategori</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-right" style="width: 18%;">Jumlah</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-left" style="width: 27%;">Deskripsi</th>
                                    <th class="px-3 py-2 text-xs border border-gray-300 text-left" style="width: 10%;">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($financeRecords as $index => $fr)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 text-xs border border-gray-300 text-center">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 text-xs border border-gray-300">{{ \Carbon\Carbon::parse($fr->tanggal)->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2 text-xs border border-gray-300 text-center">
                                            @if($fr->tipe === 'income')
                                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">
                                                    Pemasukan
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">
                                                    Pengeluaran
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-xs border border-gray-300">{{ $fr->kategori }}</td>
                                        <td class="px-3 py-2 text-xs border border-gray-300 text-right font-bold {{ $fr->tipe === 'income' ? 'text-green-700' : 'text-red-700' }}">
                                            Rp {{ number_format($fr->jumlah, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-xs border border-gray-300">{{ $fr->deskripsi ?? '-' }}</td>
                                        <td class="px-3 py-2 text-xs border border-gray-300">{{ $fr->user->name ?? 'Unknown' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-3 py-8 text-center text-gray-500 border border-gray-300">
                                            Belum ada data keuangan untuk periode ini
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Signature Section -->
                    <div class="mt-16 pt-8">
                        <div class="grid grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="text-sm font-semibold mb-16">Diketahui</div>
                                <div class="border-t-2 border-gray-800 pt-2 inline-block" style="min-width: 200px;">
                                    <div class="text-sm font-semibold">Adi</div>
                                    <div class="text-xs text-gray-600">Keuangan</div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-semibold mb-16">Diketahui</div>
                                <div class="border-t-2 border-gray-800 pt-2 inline-block" style="min-width: 200px;">
                                    <div class="text-sm font-semibold">Bagas</div>
                                    <div class="text-xs text-gray-600">Kepala Cabang</div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-semibold mb-16">Dibuat Oleh</div>
                                <div class="border-t-2 border-gray-800 pt-2 inline-block" style="min-width: 200px;">
                                    <div class="text-sm font-semibold">{{ 'Luthfi' }}</div>
                                    <div class="text-xs text-gray-600">Admin</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-12 pt-6 border-t-2 border-gray-300 text-center">
                        <p class="text-xs text-gray-600">Dokumen ini dicetak secara otomatis dan sah tanpa tanda tangan basah.</p>
                        <p class="text-xs text-gray-600 mt-1">{{ now()->format('d F Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Ini adalah preview dokumen PDF. Klik tombol <strong>"Download PDF"</strong> di atas untuk mengunduh file PDF yang sebenarnya.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
