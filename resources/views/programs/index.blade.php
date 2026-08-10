@extends('layouts.app')

@section('title', 'Kelola Program Sosial')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-handshake"></i> Program Sosial Gereja Saya</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('programs.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i> Buat Program Baru
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('programs.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari program..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Statistics --}}
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $stats['active_programs'] }}</h3>
                <p class="text-muted mb-0">Program Aktif</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ $stats['draft_programs'] }}</h3>
                <p class="text-muted mb-0">Draft</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $stats['total_registrations'] }}</h3>
                <p class="text-muted mb-0">Total Registrasi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ $stats['total_participants'] }}</h3>
                <p class="text-muted mb-0">Total Peserta</p>
            </div>
        </div>
    </div>
</div>

{{-- Programs List --}}
@if($programs->count() > 0)
    <div class="row">
        @foreach($programs as $program)
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $program->title }}</h5>
                        <span class="badge {{ $program->status === 'active' ? 'bg-success' : ($program->status === 'draft' ? 'bg-info' : 'bg-secondary') }}">
                            {{ ucfirst($program->status) }}
                        </span>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-2">{{ Str::limit($program->description, 100) }}</p>

                        <div class="row mb-3 small">
                            <div class="col-6">
                                <p class="mb-1"><i class="fas fa-calendar text-primary"></i> <strong>Tanggal</strong></p>
                                <p>{{ $program->activity_date->format('d M Y') }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><i class="fas fa-tag text-info"></i> <strong>Tipe</strong></p>
                                <p>{{ ucfirst($program->type) }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><i class="fas fa-users text-success"></i> <strong>Kapasitas</strong></p>
                                <p>{{ $program->registered_count }}/{{ $program->capacity }}</p>
                            </div>
                            <div class="col-6">
                                <p class="mb-1"><i class="fas fa-phone text-warning"></i> <strong>Kontak</strong></p>
                                <p>{{ Str::limit($program->contact_person, 15) }}</p>
                            </div>
                        </div>

                        {{-- Capacity Bar --}}
                        <div class="mb-3">
                            <small class="text-muted">Kapasitas Peserta</small>
                            <div class="progress">
                                @php
                                    $percentage = ($program->registered_count / $program->capacity) * 100;
                                @endphp
                                <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : ($percentage >= 80 ? 'bg-warning' : 'bg-success') }}" 
                                     style="width: {{ min($percentage, 100) }}%">
                                    {{ round($percentage) }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('programs.publicShow', $program) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <a href="{{ route('programs.edit', $program) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('programs.registrations', $program) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-clipboard-list"></i> Peserta ({{ $program->registered_count }})
                            </a>
                            <form action="{{ route('programs.destroy', $program) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus program?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="row">
        <div class="col-12 d-flex justify-content-center">
            {{ $programs->links() }}
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>Belum ada program sosial</h5>
            <p class="text-muted mb-3">Mulai buat program sosial untuk gereja Anda</p>
            <a href="{{ route('programs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Program Pertama
            </a>
        </div>
    </div>
@endif

@endsection
