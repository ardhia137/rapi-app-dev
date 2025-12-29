@extends('layouts.app')

@section('title', __('Edit Peran'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Peran') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Edit Peran') }}</h3>
                <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-2">Izin</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($permissions as $perm)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" @checked($role->permissions->pluck('name')->contains($perm->name)) />
                                    <span>{{ $perm->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" type="submit">Simpan</button>
                        <a class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300" href="{{ route('roles.index') }}">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
