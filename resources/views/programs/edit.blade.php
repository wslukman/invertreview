@extends('layouts.app')

@section('title', 'Edit Program Sosial')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Program Sosial</h5>
            </div>

            <form method="POST" action="{{ route('programs.update', $program) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Nama Program <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $program->title) }}"
                               placeholder="Contoh: Pemberian Beasiswa, Distribusi Sembako"
                               maxlength="255"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Program <span class="text-danger">*</span></label>
                        <select id="status" 
                                name="status" 
                                class="form-select @error('status') is-invalid @enderror"
                                required>
                            <option value="draft" {{ old('status', $program->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status', $program->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="completed" {{ old('status', $program->status) === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ old('status', $program->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status')
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
                            <option value="pelatihan" {{ old('type', $program->type) === 'pelatihan' ? 'selected' : '' }}>Pelatihan/Workshop</option>
                            <option value="pemberian_makanan" {{ old('type', $program->type) === 'pemberian_makanan' ? 'selected' : '' }}>Pemberian Makanan</option>
                            <option value="kesehatan" {{ old('type', $program->type) === 'kesehatan' ? 'selected' : '' }}>Program Kesehatan</option>
                            <option value="pendidikan" {{ old('type', $program->type) === 'pendidikan' ? 'selected' : '' }}>Program Pendidikan</option>
                            <option value="lainnya" {{ old('type', $program->type) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                  required>{{ old('description', $program->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimal 20 karakter, maksimal 3000 karakter</small>
                    </div>

                    <hr>

                    {{-- Start Date --}}
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $program->start_date ? $program->start_date->format('Y-m-d') : '') }}"
                               required>
                        @error('start_date')
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
                               value="{{ old('capacity', $program->capacity) }}"
                               min="1"
                               max="10000"
                               placeholder="Berapa orang yang bisa mengikuti program?"
                               required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">1 - 10.000 peserta (Saat ini terdaftar: {{ $program->registered_count }})</small>
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
                                   value="{{ old('contact_person', $program->contact_person) }}"
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
                                   value="{{ old('contact_phone', $program->contact_phone) }}"
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
                               value="{{ old('contact_email', $program->contact_email) }}"
                               placeholder="email@example.com">
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    {{-- Current Image --}}
                    @if($program->image_path)
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <div>
                                <img src="{{ asset('storage/' . $program->image_path) }}" 
                                     alt="Program Image" 
                                     class="img-thumbnail" 
                                     style="max-height: 200px; max-width: 300px;">
                            </div>
                            <small class="form-text text-muted">Unggah gambar baru untuk mengganti</small>
                        </div>
                    @endif

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
                        <i class="fas fa-lightbulb"></i> <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2 small">
                            <li>Perubahan status akan mempengaruhi visibilitas program untuk anggota</li>
                            <li>Tidak bisa mengurangi kapasitas di bawah jumlah peserta terdaftar</li>
                            <li>Ubah status ke "Selesai" setelah program berlangsung untuk mencatat kehadiran</li>
                        </ul>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
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
