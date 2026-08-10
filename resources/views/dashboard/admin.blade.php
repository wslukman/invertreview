@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-tachometer-alt"></i> Super Admin Dashboard</h2>
        <p class="text-muted">Pantau semua aktivitas platform United Church</p>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Gereja</h6>
                        <h2 class="mb-0">{{ $stats['total_churches'] }}</h2>
                    </div>
                    <i class="fas fa-church fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Gereja Approved</h6>
                        <h2 class="mb-0">{{ $stats['approved_churches'] }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Menunggu Approval</h6>
                        <h2 class="mb-0">{{ $stats['pending_churches'] }}</h2>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Aktivitas</h6>
                        <h2 class="mb-0">{{ $stats['total_activities'] }}</h2>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Program Sosial</h6>
                        <h2 class="mb-0">{{ $stats['total_programs'] }}</h2>
                    </div>
                    <i class="fas fa-handshake fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Komentar</h6>
                        <h2 class="mb-0">{{ $stats['total_comments'] }}</h2>
                    </div>
                    <i class="fas fa-comments fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pending Approvals --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-hourglass-half"></i> Persetujuan Gereja Menunggu 
                    <span class="badge bg-warning">{{ $stats['pending_churches'] }}</span>
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Gereja</th>
                            <th>Lokasi</th>
                            <th>Diajukan oleh</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentChurches as $church)
                            <tr>
                                <td><strong>{{ $church->name }}</strong></td>
                                <td>{{ Str::limit($church->address, 30) }}</td>
                                <td>{{ $church->submittedBy->name ?? '-' }}</td>
                                <td>{{ $church->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.churches.show', $church) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recentChurches->count() === 0)
                <div class="card-body text-center text-muted py-4">
                    Semua gereja sudah di-approve! 🎉
                </div>
            @endif

            <div class="card-footer">
                <a href="{{ route('admin.churches.pending') }}" class="btn btn-primary btn-sm">
                    Lihat Semua Persetujuan
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activities --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Aktivitas Terbaru ({{ $recentActivities->count() }})</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Judul</th>
                            <th>Gereja</th>
                            <th>Tipe</th>
                            <th>Tanggal</th>
                            <th>Pembuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                            <tr>
                                <td>{{ Str::limit($activity->title, 30) }}</td>
                                <td>{{ $activity->church->name }}</td>
                                <td>
                                    <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }}">
                                        {{ $activity->type === 'ibadah' ? 'Ibadah' : 'Sosial' }}
                                    </span>
                                </td>
                                <td>{{ $activity->activity_date->format('d M Y') }}</td>
                                <td>{{ $activity->user->name }}</td>
                                <td>
                                    <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
