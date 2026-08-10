@extends('layouts.guest')

@section('title', 'Daftar Gereja Baru')

@section('content')
<div class="hero-section">
    <h1><i class="fas fa-church"></i> Daftarkan Gereja Anda</h1>
    <p>Bergabunglah dengan jaringan gereja yang peduli melayani masyarakat</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-pencil"></i> Formulir Pendaftaran Gereja</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('register.church.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Informasi Dasar --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle"></i> Informasi Dasar</h6>

                        {{-- Nama Gereja --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Gereja <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Pastor --}}
                        <div class="mb-3">
                            <label for="pastor_name" class="form-label">Nama Pendeta/Pastor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pastor_name') is-invalid @enderror" id="pastor_name" name="pastor_name" value="{{ old('pastor_name') }}" required>
                            @error('pastor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Gereja <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            <small class="text-muted">Email untuk admin gereja login</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Telepon --}}
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx atau +62xxxxxxxxxx" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tahun Berdiri --}}
                        <div class="mb-3">
                            <label for="founded_year" class="form-label">Tahun Berdiri <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('founded_year') is-invalid @enderror" id="founded_year" name="founded_year" min="1900" max="{{ date('Y') }}" value="{{ old('founded_year') }}" required>
                            @error('founded_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-map-marker-alt"></i> Lokasi Gereja</h6>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                            <small class="text-muted">Contoh: Jl. Sudirman No. 123, Kelurahan Ilir Timur, Palembang</small>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Latitude & Longitude --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-2.9355" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="104.7438" required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="text-muted d-block">
                            <strong>Cara mendapatkan koordinat:</strong> Gunakan Google Maps, scroll ke lokasi gereja, klik kanan → koordinat akan ditampilkan
                        </small>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-file-alt"></i> Deskripsi</h6>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi Singkat Gereja <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            <small class="text-muted">Ceritakan tentang gereja Anda, visi & misi (minimal 20 karakter)</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Media (Opsional) --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><i class="fas fa-image"></i> Media (Opsional)</h6>

                        <div class="mb-3">
                            <label for="logo" class="form-label">Logo Gereja</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                            <small class="text-muted">Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 2 MB</small>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Foto Sampul Gereja</label>
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                            <small class="text-muted">Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 3 MB</small>
                            @error('cover_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i> Daftar Gereja
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="alert alert-info">
            <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Penting</h5>
            <ul class="mb-0">
                <li>Setelah mendaftar, gereja Anda akan dalam status <strong>"Pending"</strong></li>
                <li>Super Admin akan memeriksa dan <strong>meng-approve</strong> gereja Anda</li>
                <li>Email notifikasi akan dikirim ketika gereja Anda di-approve</li>
                <li>Pastikan semua data sudah benar sebelum mengirim</li>
            </ul>
        </div>
    </div>
</div>
@endsection
