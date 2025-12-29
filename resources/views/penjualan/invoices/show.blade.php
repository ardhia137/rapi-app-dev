@extends('layouts.app')

@section('title', __('Detail Penjualan'))


@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Detail Penjualan') }}</h2>
@endsection



@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-2 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ __('Detail Penjualan') }}</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('invoices.index') }}" class="inline-block px-3 py-1.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">{{ __('Kembali') }}</a>
                    </div>
                </div>

                <div class="p-2 space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Nomor Invoice</div>
                            <div class="font-semibold">{{ $invoice->invoice_number }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Pelanggan</div>
                            <div class="font-semibold">{{ $invoice->customer->nama_customer ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Tanggal Invoice</div>
                            <div class="font-semibold">{{ $invoice->tanggal_invoice }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Jatuh Tempo</div>
                            <div class="font-semibold">{{ $invoice->tanggal_jatuh_tempo }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Pembayaran</div>
                            @php $status = $invoice->status_pembayaran; @endphp
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
                        <div>
                            <div class="text-gray-600">Metode Pembayaran</div>
                            <div class="font-semibold">{{ ucfirst($invoice->metode_pembayaran ?? '-') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Status Setor</div>
                            @php $isSetor = ($invoice->status_setor ?? 'belum') === 'sudah'; @endphp
                            <span class="inline-block px-2 py-1 rounded text-xs {{ $isSetor ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $isSetor ? 'Setor' : 'Belum' }}</span>
                        </div>
                        <div>
                            <div class="text-gray-600">Bukti Pembayaran</div>
                            {!! $invoice->bukti_setor
                                ? '<a target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline" href="'.asset('storage/'.$invoice->bukti_setor).'">Lihat</a>'
                                : '-' !!}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Produk</th>
                                    <th class="px-4 py-2 border">Kuantitas</th>
                                    <th class="px-4 py-2 border">Harga</th>
                                    <th class="px-4 py-2 border">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr class="text-center hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $item->product->nama_produk ?? $item->product->nama ?? 'Produk #'.$item->product_id }}</td>
                                        <td class="px-4 py-2 border">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 border">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 border text-right font-semibold">
                                        Ongkos Kirim <span class="text-xs text-gray-500">(+)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-green-600">
                                        + Rp {{ number_format($invoice->ongkos_kirim ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="px-4 py-2 border text-right font-semibold">
                                        Diskon <span class="text-xs text-gray-500">(−)</span>
                                    </td>
                                    <td class="px-4 py-2 border font-semibold text-red-600">
                                        − Rp {{ number_format($invoice->diskon ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr class="bg-gray-50">
                                    <td colspan="3" class="px-4 py-2 border text-right font-bold">
                                        Grand Total
                                    </td>
                                    <td class="px-4 py-2 border font-bold">
                                        Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                    @if($invoice->status_pembayaran === 'cancelled')
                    <div>
                        <div class="text-gray-600">Alasan Batal</div>
                        <div class="font-semibold">{{ $invoice->alasan_cancel }}</div>
                    </div>
                    @endif
                    <div>
                        <a href="{{ route('invoices.pdf', $invoice->id) }}" target="_blank" rel="noopener" class="inline-block px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700">{{ __('Buat Invoice') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection