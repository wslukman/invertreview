@extends('layouts.app')

@section('title', 'Buat Aktivitas Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> Buat Aktivitas Baru</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('activities.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Ibadah Natal 2026" maxlength="255" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Content --}}
                    <div class="mb-3">
                        <label for="content" class="form-label">Konten/Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" placeholder="Tulis deskripsi aktivitas..." required>{{ old('content') }}</textarea>
                        <small class="text-muted">Minimal 10 karakter, maksimal 5000 karakter</small>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Aktivitas <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="ibadah" {{ old('type') === 'ibadah' ? 'selected' : '' }}>Kegiatan Ibadah</option>
                            <option value="kegiatan_sosial" {{ old('type') === 'kegiatan_sosial' ? 'selected' : '' }}>Kegiatan Sosial</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Activity Date --}}
                    <div class="mb-3">
                        <label for="activity_date" class="form-label">Tanggal Aktivitas <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('activity_date') is-invalid @enderror" id="activity_date" name="activity_date" value="{{ old('activity_date') }}" required>
                        <small class="text-muted">Harus hari ini atau di masa depan</small>
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Image (Optional) --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar (Opsional)</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF | Ukuran maksimal: 2 MB</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Buat Aktivitas
                        </button>
                        <a href="{{ route('activities.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="alert alert-info mt-3">
            <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips Membuat Aktivitas</h5>
            <ul class="mb-0">
                <li>Tulis judul yang menarik dan deskriptif</li>
                <li>Deskripsi yang jelas membantu member memahami aktivitas</li>
                <li>Tambahkan gambar untuk membuat posting lebih menarik</li>
                <li>Pilih tipe aktivitas yang sesuai</li>
            </ul>
        </div>
    </div>
</div>
@endsection
