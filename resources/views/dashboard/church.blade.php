@extends('layouts.app')

@section('title', 'Dashboard Gereja')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-church"></i> Dashboard Gereja</h2>
        <p class="text-muted">{{ auth()->user()->church->name ?? 'Gereja' }}</p>
    </div>
</div>

{{-- Church Status Alert --}}
@if(auth()->user()->church->status === 'approved')
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <strong>Gereja Approved!</strong> Gereja Anda sudah terdaftar dan dapat melihat semua fitur.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@elseif(auth()->user()->church->status === 'pending')
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-hourglass-half"></i> <strong>Menunggu Persetujuan</strong> Gereja Anda sedang dalam proses verifikasi oleh super admin.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Statistics --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Total Anggota</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_members'] }}</h2>
                    </div>
                    <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Aktivitas</h6>
                        <h2 class="mb-0 text-success">{{ $stats['total_activities'] }}</h2>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Program Sosial</h6>
                        <h2 class="mb-0 text-info">{{ $stats['total_programs'] }}</h2>
                    </div>
                    <i class="fas fa-handshake fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Registrasi Program</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['total_registrations'] }}</h2>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('activities.create') }}" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-plus"></i> Buat Aktivitas
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('programs.create') }}" class="btn btn-success btn-block w-100">
                            <i class="fas fa-plus"></i> Buat Program
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('programs.index') }}" class="btn btn-info btn-block w-100">
                            <i class="fas fa-list"></i> Registrasi Program
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('churches.show', auth()->user()->church) }}" class="btn btn-warning btn-block w-100">
                            <i class="fas fa-edit"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activities --}}
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Aktivitas Terbaru</h5>
            </div>

            @if($recentActivities->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentActivities as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">{{ $activity->title }}</h6>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($activity->content, 80) }}</p>
                            <div>
                                <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }}">
                                    {{ $activity->type === 'ibadah' ? 'Ibadah' : 'Sosial' }}
                                </span>
                                <span class="badge bg-secondary">{{ $activity->comments_count }} Komentar</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Aktivitas
                    </a>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Belum ada aktivitas. <a href="{{ route('activities.create') }}">Buat sekarang</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Programs --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-handshake"></i> Program Sosial</h5>
            </div>

            @if($recentPrograms->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentPrograms as $program)
                        <a href="{{ route('programs.edit', $program) }}" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($program->title, 25) }}</h6>
                                    <small class="text-muted">
                                        @if($program->status === 'active')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $program->status }}</span>
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-light text-dark">{{ $program->registered_count }}/{{ $program->capacity }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a href="{{ route('programs.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Belum ada program. <a href="{{ route('programs.create') }}">Buat sekarang</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Members List --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users"></i> Anggota Gereja ({{ $stats['total_members'] }})</h5>
            </div>

            @if($churchMembers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Nomor Telepon</th>
                                <th>Bergabung</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($churchMembers as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->phone ?? '-' }}</td>
                                    <td>{{ $member->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($member->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Belum ada anggota terdaftar
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
