@extends('layouts.app')

@section('title', 'Edit Aktivitas')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Aktivitas</h5>
            </div>

            <form method="POST" action="{{ route('activities.update', $activity) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Aktivitas <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $activity->title) }}"
                               placeholder="Masukkan judul aktivitas"
                               maxlength="255"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="mb-3">
                        <label for="type" class="form-label">Tipe Aktivitas <span class="text-danger">*</span></label>
                        <select id="type" 
                                name="type" 
                                class="form-select @error('type') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="ibadah" {{ old('type', $activity->type) === 'ibadah' ? 'selected' : '' }}>Kegiatan Ibadah</option>
                            <option value="kegiatan_sosial" {{ old('type', $activity->type) === 'kegiatan_sosial' ? 'selected' : '' }}>Kegiatan Sosial</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Activity Date --}}
                    <div class="mb-3">
                        <label for="activity_date" class="form-label">Tanggal Aktivitas <span class="text-danger">*</span></label>
                        <input type="date" 
                               id="activity_date" 
                               name="activity_date" 
                               class="form-control @error('activity_date') is-invalid @enderror"
                               value="{{ old('activity_date', $activity->activity_date->format('Y-m-d')) }}"
                               required>
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Tanggal harus hari ini atau di masa depan</small>
                    </div>

                    {{-- Content --}}
                    <div class="mb-3">
                        <label for="content" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea id="content" 
                                  name="content" 
                                  class="form-control @error('content') is-invalid @enderror"
                                  rows="6"
                                  placeholder="Masukkan deskripsi aktivitas secara detail..."
                                  required>{{ old('content', $activity->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimal 10 karakter, maksimal 5000 karakter</small>
                    </div>

                    {{-- Current Image --}}
                    @if($activity->image_path)
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <div>
                                <img src="{{ asset('storage/' . $activity->image_path) }}" 
                                     alt="Activity Image" 
                                     class="img-thumbnail" 
                                     style="max-height: 200px; max-width: 300px;">
                            </div>
                            <small class="form-text text-muted">Unggah gambar baru untuk mengganti</small>
                        </div>
                    @endif

                    {{-- Image Upload --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Aktivitas (Opsional)</label>
                        <input type="file" 
                               id="image" 
                               name="image" 
                               class="form-control @error('image') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               onchange="previewImage(event)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF (Maksimal 2MB)</small>
                        <div id="imagePreview" class="mt-2"></div>
                    </div>

                    {{-- Info Box --}}
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Tips:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Berikan judul yang menarik dan deskriptif</li>
                            <li>Jelaskan tujuan, waktu, dan tempat aktivitas dengan jelas</li>
                            <li>Tambahkan gambar untuk membuat aktivitas lebih menarik</li>
                            <li>Pastikan informasi akurat dan terkini</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-height: 200px; max-width: 300px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '';
    }
}
</script>

@endsection
