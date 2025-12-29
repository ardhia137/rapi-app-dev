@extends('layouts.app')

@section('title', __('Detail Surat Jalan'))

@section('header')
<h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Surat Jalan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Detail Surat Jalan') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('surat-jalan.index') }}" class="inline-block px-3 py-1.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('Kembali') }}</a>
                    </div>
                </div>

                <div class="p-4 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Nomor Surat Jalan</div>
                            <div class="font-semibold">{{ $suratJalan->nomor_surat_jalan ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Pelanggan</div>
                            <div class="font-semibold">{{ $suratJalan->customer->nama_customer ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Nomor Invoice</div>
                            <div class="font-semibold">{{ $suratJalan->invoice->invoice_number ?? $suratJalan->invoice_id }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tanggal</div>
                            <div class="font-semibold">{{ $suratJalan->tanggal }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Pembayaran</div>
                            @php $status = $suratJalan->status; @endphp
                            @if ($status === 'sudah dikirim')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800">Sudah Dikirim</span>
                            @elseif ($status === 'belum dikirim')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800">Belum Dikirim</span>
                            @elseif ($status === 'cancel')
                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-800">Dibatalkan</span>
                            @else
                            <span class="inline-block px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">{{ ucfirst($status ?? '-') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">No Invoice</th>
                                    <th class="px-4 py-2 border">Sub Total Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="text-center hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $suratJalan->invoice->invoice_number ?? $suratJalan->invoice_id }}</td>
                                    <td class="px-4 py-2 border">
                                        Rp {{ number_format(($suratJalan->invoice && $suratJalan->invoice->items) ? $suratJalan->invoice->items->sum('sub_total') : 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1" class="px-4 py-2 border text-right font-semibold">
                                        Ongkos Kirim <span class="text-xs text-gray-500">(+)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-green-600">
                                        + Rp {{ number_format($suratJalan->invoice->ongkos_kirim ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="1" class="px-4 py-2 border text-right font-semibold">
                                        Diskon <span class="text-xs text-gray-500">(−)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-red-600 right">
                                        − Rp {{ number_format($suratJalan->invoice->diskon ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr class="bg-gray-50">
                                    <td colspan="1" class="px-4 py-2 border text-right font-bold">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2 border font-bold">
                                        Rp {{ number_format((($suratJalan->invoice && $suratJalan->invoice->items) ? $suratJalan->invoice->items->sum('sub_total') : 0) - ($suratJalan->invoice->diskon ?? 0) + ($suratJalan->invoice->ongkos_kirim ?? 0), 0, ',', '.') }}
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>

                    @if($suratJalan->status_pembayaran === 'cancel')
                    <div>
                        <div class="text-gray-600">Alasan Batal</div>
                        <div class="font-semibold">{{ $suratJalan->alasan_cancel }}</div>
                    </div>
                    @endif
                    <div>
                        <a href="{{ route('surat-jalan.pdf', $suratJalan->id) }}" target="_blank" rel="noopener" class="inline-block px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700">{{ __('Buat Surat Jalan') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection