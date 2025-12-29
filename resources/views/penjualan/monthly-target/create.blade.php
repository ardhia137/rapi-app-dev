@extends('layouts.app')

@section('title', __('Buat Target Bulanan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Buat Target Bulanan') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-auto">
                <h3 class="mb-4">{{ __('Buat Target Bulanan') }}</h3>
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded bg-red-50 text-red-700">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('monthly-targets.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nama') }}</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('Dari Tanggal') }}</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('Sampai Tanggal') }}</label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="target_amount_display" class="block text-sm font-medium text-gray-700">{{ __('Target Bulanan') }}</label>
                        <div class="mt-1 flex items-center">
                            <span class="px-2 py-2 bg-gray-100 border border-gray-300 rounded-l">Rp</span>
                            <input type="text" id="target_amount_display" class="w-full border-gray-300 rounded-r-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" placeholder="0" />
                            <input type="hidden" name="target_amount" id="target_amount" value="{{ old('target_amount', 0) }}" />
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Catatan') }}</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">{{ old('notes') }}</textarea>
                    </div>
                    <div class="flex items-center gap-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
                        <a href="{{ route('monthly-targets.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        function formatRupiahRaw(n){ return new Intl.NumberFormat('id-ID').format(Math.round(n || 0)); }
        function unformatRupiah(str){ return (str || '').toString().replace(/[^0-9]/g,''); }
        const disp = document.getElementById('target_amount_display');
        const hid = document.getElementById('target_amount');
        if (disp && hid) {
            // init from hidden
            const raw = hid.value || '0';
            disp.value = formatRupiahRaw(raw);
            disp.addEventListener('input', function(e){
                const val = unformatRupiah(e.target.value);
                hid.value = val || 0;
                e.target.value = val ? formatRupiahRaw(val) : '';
            });
        }
    })();
</script>
@endpush
