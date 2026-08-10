@extends('layouts.app')

@section('title', 'Buat Program Sosial')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-handshake"></i> Buat Program Sosial Baru</h5>
            </div>

            <form method="POST" action="{{ route('programs.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="card-body">
                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}"
                               placeholder="Contoh: Pemberian Beasiswa, Distribusi Sembako"
                               maxlength="255"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis Program <span class="text-danger">*</span></label>
                        <select id="type" 
                                name="type" 
                                class="form-select @error('type') is-invalid @enderror"
                                required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="pelatihan" {{ old('type') === 'pelatihan' ? 'selected' : '' }}>Pelatihan/Workshop</option>
                            <option value="pemberian_makanan" {{ old('type') === 'pemberian_makanan' ? 'selected' : '' }}>Pemberian Makanan</option>
                            <option value="kesehatan" {{ old('type') === 'kesehatan' ? 'selected' : '' }}>Program Kesehatan</option>
                            <option value="pendidikan" {{ old('type') === 'pendidikan' ? 'selected' : '' }}>Program Pendidikan</option>
                            <option value="lainnya" {{ old('type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi Program <span class="text-danger">*</span></label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="5"
                                  placeholder="Jelaskan tujuan, manfaat, dan detail program..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimal 20 karakter, maksimal 3000 karakter</small>
                    </div>

                    <hr>

                    {{-- Activity Date --}}
                    <div class="mb-3">
                        <label for="activity_date" class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               id="activity_date" 
                               name="activity_date" 
                               class="form-control @error('activity_date') is-invalid @enderror"
                               value="{{ old('activity_date') }}"
                               required>
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Capacity --}}
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Kapasitas Peserta <span class="text-danger">*</span></label>
                        <input type="number" 
                               id="capacity" 
                               name="capacity" 
                               class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', 50) }}"
                               min="1"
                               max="10000"
                               placeholder="Berapa orang yang bisa mengikuti program?"
                               required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">1 - 10.000 peserta</small>
                    </div>

                    <hr>

                    <div class="row">
                        {{-- Contact Person --}}
                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Narahubung <span class="text-danger">*</span></label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   class="form-control @error('contact_person') is-invalid @enderror"
                                   value="{{ old('contact_person') }}"
                                   placeholder="Nama penanggung jawab"
                                   required>
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Contact Phone --}}
                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" 
                                   id="contact_phone" 
                                   name="contact_phone" 
                                   class="form-control @error('contact_phone') is-invalid @enderror"
                                   value="{{ old('contact_phone') }}"
                                   placeholder="08xx atau +62xx"
                                   required>
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Format: 08xx atau +62xx</small>
                        </div>
                    </div>

                    {{-- Contact Email --}}
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email Kontak (Opsional)</label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               class="form-control @error('contact_email') is-invalid @enderror"
                               value="{{ old('contact_email') }}"
                               placeholder="email@example.com">
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Image Upload --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Program (Opsional)</label>
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
                        <i class="fas fa-lightbulb"></i> <strong>Tips Membuat Program:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Berikan judul yang jelas dan deskriptif</li>
                            <li>Jelaskan dengan detail tujuan dan manfaat program</li>
                            <li>Tentukan kapasitas realistis peserta</li>
                            <li>Sediakan kontak yang mudah dihubungi peserta</li>
                            <li>Tambahkan gambar/banner program untuk menarik perhatian</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Buat Program
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
