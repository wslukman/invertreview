@extends('layouts.guest')

@section('title', 'Cari Program Sosial')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-handshake"></i> Program Sosial Gratis</h2>
            <p class="text-muted">Temukan program sosial dari gereja-gereja di sekitar Anda</p>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('programs.public') }}" class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Cari program..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">Semua Jenis</option>
                                <option value="pelatihan" {{ request('type') === 'pelatihan' ? 'selected' : '' }}>Pelatihan</option>
                                <option value="pemberian_makanan" {{ request('type') === 'pemberian_makanan' ? 'selected' : '' }}>Pemberian Makanan</option>
                                <option value="kesehatan" {{ request('type') === 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                <option value="pendidikan" {{ request('type') === 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="church" class="form-select">
                                <option value="">Semua Gereja</option>
                                @foreach($churches as $church)
                                    <option value="{{ $church->id }}" {{ request('church') == $church->id ? 'selected' : '' }}>
                                        {{ $church->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Badge --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle"></i> 
                Menampilkan <strong>{{ $programs->total() }}</strong> program dari <strong>{{ $churches->count() }}</strong> gereja terdaftar.
            </div>
        </div>
    </div>

    {{-- Programs Grid --}}
    @if($programs->count() > 0)
        <div class="row">
            @foreach($programs as $program)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow" style="transition: all 0.3s border: none;">
                        {{-- Image --}}
                        @if($program->image_path)
                            <img src="{{ asset('storage/' . $program->image_path) }}" 
                                 alt="{{ $program->title }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-handshake text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <h5 class="card-title mb-2 text-primary">{{ Str::limit($program->title, 50) }}</h5>
                                <span class="badge bg-soft-info text-info border border-info">{{ ucfirst(str_replace('_', ' ', $program->type)) }}</span>
                            </div>

                            <p class="card-text text-muted small mb-3">
                                {{ Str::limit($program->description, 80) }}
                            </p>

                            {{-- Church Info --}}
                            <div class="mb-3 p-2 bg-light rounded">
                                <p class="mb-1 small"><i class="fas fa-church text-primary"></i> <strong>{{ $program->church->name ?? 'Gereja United' }}</strong></p>
                                <p class="mb-0 text-muted small"><i class="fas fa-map-marker-alt"></i> {{ Str::limit($program->church->address ?? 'Lokasi tidak tersedia', 40) }}</p>
                            </div>

                            {{-- Program Details (BAGIAN PERBAIKAN) --}}
                            <div class="row text-center mb-3 small">
                                <div class="col-6 border-end">
                                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Tanggal Mulai</p>
                                    <p class="mb-0 fw-bold">
                                        <i class="fas fa-calendar-alt text-primary"></i> 
                                        {{-- PERBAIKAN: Menggunakan start_date dan null-safe operator --}}
                                        {{ $program->start_date?->format('d M Y') ?? 'Segera' }}
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Kapasitas</p>
                                    <p class="mb-0">
                                        <i class="fas fa-users text-primary"></i> 
                                        <span class="badge bg-success">{{ $program->registrations_count ?? 0 }}/{{ $program->capacity }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Capacity Bar --}}
                            <div class="mb-3">
                                @php
                                    $count = $program->registrations_count ?? 0;
                                    $cap = $program->capacity > 0 ? $program->capacity : 1;
                                    $percentage = ($count / $cap) * 100;
                                @endphp
                                <div class="progress" style="height: 10px; border-radius: 10px;">
                                    <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-success') }}" 
                                         style="width: {{ min($percentage, 100) }}%">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">{{ round($percentage) }}% Terisi</small>
                                    @if($percentage >= 100)
                                        <small class="text-danger fw-bold">Penuh!</small>
                                    @endif
                                </div>
                            </div>

                            {{-- Contact Info --}}
                            <div class="mt-auto pt-2 border-top small text-muted">
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-user-circle"></i> {{ $program->contact_person ?? 'Admin' }}</span>
                                    <span><i class="fas fa-phone-alt"></i> {{ $program->contact_phone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3">
                            <a href="{{ route('programs.publicShow', $program) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                Detail Program <i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $programs->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5>Tidak ada program ditemukan</h5>
                <p class="text-muted mb-3">Cobalah mengubah kriteria filter atau kata kunci pencarian Anda.</p>
                <a href="{{ route('programs.public') }}" class="btn btn-primary rounded-pill">Reset Filter</a>
            </div>
        </div>
    @endif
</div>

<style>
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
}
.bg-soft-info {
    background-color: rgba(13, 202, 240, 0.1);
}
</style>
@endsection