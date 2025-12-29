@extends('layouts.app')

@section('title', __('Edit Pengguna'))

@section('header')
    <h2 class="hidden sm:block text-xl font-semibold text-gray-800">{{ __('Edit Pengguna') }}</h2>
@endsection

@section('content')
<div class="py-2">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-3xl">
                <h3 class="mb-4">{{ __('Edit Pengguna') }}</h3>
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border rounded" required />
                        @error('email')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Password (optional)</label>
                        <input type="password" name="password" class="w-full px-3 py-2 border rounded" />
                        @error('password')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded">
                            <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block mb-2">Peran</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked($user->roles->pluck('name')->contains($role->name)) />
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