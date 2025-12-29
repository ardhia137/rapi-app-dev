@extends('layouts.app')

@section('title', __('Tambah Pengguna'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Tambah Pengguna') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Tambah Pengguna') }}</h3>
                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border rounded" required />
                        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded" required />
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Password</label>
                        <input type="password" name="password" class="w-full px-3 py-2 border rounded" required />
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-2">Peran</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" />
                                    <span>{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Simpan</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('users.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection