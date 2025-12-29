@extends('layouts.app')

@section('title', __('Edit Pelanggan'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Pelanggan') }}</h2>
@endsection

@section('content')
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <h3 class="mb-4">{{ __('Edit Pelanggan') }}</h3>
                    <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nama_customer" class="block text-sm font-medium text-gray-700">{{ __('Nama Pelanggan') }}</label>
                            <input type="text" name="nama_customer" id="nama_customer" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required value="{{ old('nama_customer', $customer->nama_customer) }}">
                        </div>
                        <div class="mb-4">
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">{{ __('No. HP') }}</label>
                            <input type="text" name="no_hp" id="no_hp" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required value="{{ old('no_hp', $customer->no_hp) }}">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('E-mail') }}</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"  value="{{ old('email', $customer->email) }}">
                        </div>
                        <div class="mb-4">
                            <label for="kategori_pelanggan" class="block text-sm font-medium text-gray-700">{{ __('Kategori Pelanggan') }}</label>
                            <select name="kategori_pelanggan" id="kategori_pelanggan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>
                                <option value="Toko" {{ old('kategori_pelanggan', $customer->kategori_pelanggan) == 'Toko' ? 'selected' : '' }}>{{ __('Toko') }}</option>
                                <option value="Konsumen" {{ old('kategori_pelanggan', $customer->kategori_pelanggan) == 'Konsumen' ? 'selected' : '' }}>{{ __('Konsumen') }}</option>
                                <option value="Aplikator/Tukang" {{ old('kategori_pelanggan', $customer->kategori_pelanggan) == 'Aplikator/Tukang' ? 'selected' : '' }}>{{ __('Aplikator/Tukang') }}</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">{{ __('Alamat') }}</label>
                            <textarea name="alamat" id="alamat" cols="30" rows="5" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm" required>{{ old('alamat', $customer->alamat) }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="point" class="block text-sm font-medium text-gray-700">{{ __('Poin') }}</label>
                            <input type="number" name="point" id="point" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"  value="{{ old('point', $customer->point) }}">
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ __('Simpan') }}</button>
                            <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection