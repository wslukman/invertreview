@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Pengaturan Profil</h2>
    
    @if (session('status') === 'profile-updated')
        <div class="bg-green-100 text-green-800 p-3 mb-4 rounded">
            Profil berhasil diperbarui!
        </div>
    @endif

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="max-w-2xl bg-white p-6 rounded shadow">
        @csrf
        @method('patch')

        <div class="mb-4">
            <label class="block text-gray-700">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded p-2">
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded p-2">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        @if($user->hasRole('church_admin'))
            <div class="mt-6 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Gereja</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-700">Nama Gereja</label>
                    <input type="text" name="church_name" value="{{ old('church_name', $user->church?->name) }}" class="w-full border rounded p-2" required>
                    @error('church_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Nama Pendeta</label>
                    <input type="text" name="pastor_name" value="{{ old('pastor_name', $user->church?->pastor_name) }}" class="w-full border rounded p-2">
                    @error('pastor_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Telepon Gereja</label>
                    <input type="text" name="church_phone" value="{{ old('church_phone', $user->church?->phone) }}" class="w-full border rounded p-2">
                    @error('church_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Email Gereja (Publik)</label>
                    <input type="email" name="church_email" value="{{ old('church_email', $user->church?->email) }}" class="w-full border rounded p-2">
                    @error('church_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Alamat Lengkap</label>
                    <textarea name="church_address" class="w-full border rounded p-2" rows="3">{{ old('church_address', $user->church?->address) }}</textarea>
                    @error('church_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700">Latitude</label>
                            <input type="text" name="church_latitude" value="{{ old('church_latitude', $user->church?->latitude) }}" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-gray-700">Longitude</label>
                            <input type="text" name="church_longitude" value="{{ old('church_longitude', $user->church?->longitude) }}" class="w-full border rounded p-2">
                        </div>
                    </div>
                    @error('church_latitude') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Tahun Berdiri</label>
                    <input type="number" name="church_founded_year" value="{{ old('church_founded_year', $user->church?->founded_year) }}" class="w-full border rounded p-2">
                    @error('church_founded_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Deskripsi Gereja</label>
                    <textarea name="church_description" class="w-full border rounded p-2" rows="4">{{ old('church_description', $user->church?->description) }}</textarea>
                    @error('church_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700">Link Sosial Media</label>
                    <input type="url" name="social_media" value="{{ old('social_media', $user->church?->social_media) }}" class="w-full border rounded p-2" placeholder="https://instagram.com/nama_gereja">
                    @error('social_media') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700">Logo Gereja</label>
                    <input type="file" name="church_logo" class="w-full border rounded p-2">
                    @if($user->church?->logo_path)
                        <img src="{{ asset('storage/' . $user->church->logo_path) }}" class="h-20 mt-2">
                    @endif
                    @error('church_logo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        @endif

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection
