@extends('layouts.app')

@section('title', 'Dashboard Member')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-user"></i> Dashboard Member</h2>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>
</div>

{{-- Statistics --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Aktivitas Saya</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['my_activities'] }}</h2>
                    </div>
                    <i class="fas fa-pencil-alt fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Komentar Saya</h6>
                        <h2 class="mb-0 text-success">{{ $stats['my_comments'] }}</h2>
                    </div>
                    <i class="fas fa-comments fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Program Terdaftar</h6>
                        <h2 class="mb-0 text-info">{{ $stats['registered_programs'] }}</h2>
                    </div>
                    <i class="fas fa-clipboard-list fa-2x text-info opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Gereja Saya</h6>
                        <h2 class="mb-0 text-warning">{{ auth()->user()->church ? 1 : 0 }}</h2>
                    </div>
                    <i class="fas fa-church fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Church Info (if has church) --}}
@if(auth()->user()->church)
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-left border-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            @if(auth()->user()->church->logo_path)
                                <img src="{{ asset('storage/' . auth()->user()->church->logo_path) }}" 
                                     alt="Logo" class="img-fluid rounded" style="max-height: 150px;">
                            @else
                                <div class="bg-light rounded p-4">
                                    <i class="fas fa-church fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h5>{{ auth()->user()->church->name }}</h5>
                            <p class="text-muted small">{{ Str::limit(auth()->user()->church->address, 100) }}</p>
                            <div class="mb-2">
                                @if(auth()->user()->church->phone)
                                    <p class="mb-1"><i class="fas fa-phone text-primary"></i> {{ auth()->user()->church->phone }}</p>
                                @endif
                                @if(auth()->user()->church->email)
                                    <p class="mb-1"><i class="fas fa-envelope text-primary"></i> {{ auth()->user()->church->email }}</p>
                                @endif
                            </div>
                            <a href="{{ route('churches.show', auth()->user()->church) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Lihat Profil Gereja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Status Gereja</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Status:</strong>
                        @if(auth()->user()->church->status === 'approved')
                            <span class="badge bg-success">Terdaftar</span>
                        @elseif(auth()->user()->church->status === 'pending')
                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                        @elseif(auth()->user()->church->status === 'rejected')
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </p>
                    <p class="mb-0 small text-muted">
                        <strong>Didaftarkan:</strong> {{ auth()->user()->church->created_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Quick Actions --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Navigasi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="{{ route('activities.index') }}" class="btn btn-outline-primary btn-block w-100 small">
                            <i class="fas fa-list"></i><br>Aktivitas
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="{{ route('churches.search') }}" class="btn btn-outline-success btn-block w-100 small">
                            <i class="fas fa-map-marker-alt"></i><br>Cari Gereja
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="{{ route('programs.public') }}" class="btn btn-outline-info btn-block w-100 small">
                            <i class="fas fa-handshake"></i><br>Program
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="#my-registrations" class="btn btn-outline-warning btn-block w-100 small">
                            <i class="fas fa-clipboard-list"></i><br>Registrasi
                        </a>
                    </div>
                    <div class="col-md-2 col-sm-4 col-6">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-block w-100 small">
                            <i class="fas fa-user-circle"></i><br>Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- My Activities & Comments --}}
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-pencil-alt"></i> Aktivitas Saya ({{ $stats['my_activities'] }})</h5>
            </div>

            @if($myActivities->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($myActivities as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0">{{ Str::limit($activity->title, 35) }}</h6>
                                <small class="text-muted">{{ $activity->created_at->format('d M') }}</small>
                            </div>
                            <p class="mb-1 text-muted small">{{ Str::limit($activity->content, 60) }}</p>
                            <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }} small">
                                {{ $activity->type === 'ibadah' ? 'Ibadah' : 'Sosial' }}
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Belum membuat aktivitas apapun
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-comments"></i> Aktivitas Feed</h5>
            </div>

            @if($activityFeed->count() > 0)
                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    @foreach($activityFeed as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="list-group-item list-group-item-action py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 small">{{ Str::limit($activity->title, 40) }}</h6>
                                    <small class="text-muted d-block">{{ $activity->church->name }}</small>
                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-light text-dark small">{{ $activity->comments_count }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="card-footer">
                    <a href="{{ route('activities.index') }}" class="btn btn-sm btn-outline-primary">
                        Jelajahi Lebih Banyak
                    </a>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Belum ada aktivitas
                </div>
            @endif
        </div>
    </div>
</div>

{{-- My Registrations --}}
<div class="row" id="my-registrations">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Program yang Saya Ikuti ({{ $stats['registered_programs'] }})</h5>
            </div>

            @if($myRegistrations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Program</th>
                                <th>Gereja</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myRegistrations as $registration)
                                <tr>
                                    <td><strong>{{ Str::limit($registration->program->title, 25) }}</strong></td>
                                    <td>{{ $registration->program->church->name }}</td>
                                    <td>
                                        <span class="badge bg-info small">
                                            {{ ucfirst($registration->program->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $registration->program->activity_date->format('d M Y') }}</td>
                                    <td>
                                        @if($registration->status === 'registered')
                                            <span class="badge bg-success">Terdaftar</span>
                                        @elseif($registration->status === 'attended')
                                            <span class="badge bg-primary">Hadir</span>
                                        @elseif($registration->status === 'cancelled')
                                            <span class="badge bg-danger">Batal</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('programs.publicShow', $registration->program) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($registration->status === 'registered')
                                            <form action="{{ route('registrations.destroy', $registration) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan pendaftaran?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="card-body text-center text-muted py-4">
                    Anda belum terdaftar di program apapun. <a href="{{ route('programs.public') }}">Cari program sekarang</a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
