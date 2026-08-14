@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-4"><i class="fas fa-user-cog"></i> Pengaturan Profil</h2>
            
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> Profil berhasil diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($user->hasRole('church_admin'))
                            <hr class="my-4">
                            <h4 class="mb-4 text-primary"><i class="fas fa-church"></i> Informasi Gereja</h4>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Gereja</label>
                                <input type="text" name="church_name" value="{{ old('church_name', $user->church?->name) }}" class="form-control @error('church_name') is-invalid @enderror" required>
                                @error('church_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nama Pendeta</label>
                                    <input type="text" name="pastor_name" value="{{ old('pastor_name', $user->church?->pastor_name) }}" class="form-control @error('pastor_name') is-invalid @enderror">
                                    @error('pastor_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Telepon Gereja</label>
                                    <input type="text" name="church_phone" value="{{ old('church_phone', $user->church?->phone) }}" class="form-control @error('church_phone') is-invalid @enderror">
                                    @error('church_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Email Gereja (Publik)</label>
                                <input type="email" name="church_email" value="{{ old('church_email', $user->church?->email) }}" class="form-control @error('church_email') is-invalid @enderror">
                                @error('church_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Alamat Lengkap</label>
                                <textarea name="church_address" class="form-control @error('church_address') is-invalid @enderror" rows="3">{{ old('church_address', $user->church?->address) }}</textarea>
                                @error('church_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Latitude</label>
                                    <input type="text" name="church_latitude" value="{{ old('church_latitude', $user->church?->latitude) }}" class="form-control @error('church_latitude') is-invalid @enderror">
                                    @error('church_latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Longitude</label>
                                    <input type="text" name="church_longitude" value="{{ old('church_longitude', $user->church?->longitude) }}" class="form-control @error('church_longitude') is-invalid @enderror">
                                    @error('church_longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tahun Berdiri</label>
                                <input type="number" name="church_founded_year" value="{{ old('church_founded_year', $user->church?->founded_year) }}" class="form-control @error('church_founded_year') is-invalid @enderror">
                                @error('church_founded_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Gereja</label>
                                <textarea name="church_description" class="form-control @error('church_description') is-invalid @enderror" rows="4">{{ old('church_description', $user->church?->description) }}</textarea>
                                @error('church_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Link Sosial Media</label>
                                <input type="url" name="social_media" value="{{ old('social_media', $user->church?->social_media) }}" class="form-control @error('social_media') is-invalid @enderror" placeholder="https://instagram.com/nama_gereja">
                                @error('social_media') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Logo Gereja</label>
                                <input type="file" name="church_logo" class="form-control @error('church_logo') is-invalid @enderror">
                                @if($user->church?->logo_path)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $user->church->logo_path) }}" class="img-thumbnail" style="max-height: 80px;" alt="Logo Gereja">
                                    </div>
                                @endif
                                @error('church_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
