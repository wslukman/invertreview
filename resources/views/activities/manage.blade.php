@extends('layouts.app')

@section('title', 'Kelola Aktivitas')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-calendar-alt"></i> Aktivitas Gereja Saya</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('activities.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus"></i> Buat Aktivitas Baru
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('activities.manage') }}" class="row g-2">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari aktivitas..." value="{{ request('search') }}">
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
                <h3 class="text-primary">{{ $stats['active_activities'] }}</h3>
                <p class="text-muted mb-0">Aktivitas Dipublikasikan</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info">{{ $stats['draft_activities'] }}</h3>
                <p class="text-muted mb-0">Draft</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $stats['total_views'] }}</h3>
                <p class="text-muted mb-0">Total Views</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning">{{ $stats['total_comments'] }}</h3>
                <p class="text-muted mb-0">Total Komentar</p>
            </div>
        </div>
    </div>
</div>

{{-- Activities List --}}
@if($activities->count() > 0)
    <div class="row">
        @foreach($activities as $activity)
            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $activity->title }}</h5>
                        <span class="badge {{ $activity->is_published ? 'bg-success' : 'bg-info' }}">
                            {{ $activity->is_published ? 'Dipublikasikan' : 'Draft' }}
                        </span>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-2">{{ Str::limit($activity->content, 100) }}</p>

                        <div class="row mb-3 small">
                            <div class="col-4">
                                <p class="mb-1"><i class="fas fa-calendar text-primary"></i> <strong>Tanggal</strong></p>
                                <p>{{ $activity->activity_date ? $activity->activity_date->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="col-4">
                                <p class="mb-1"><i class="fas fa-tag text-info"></i> <strong>Tipe</strong></p>
                                <p>{{ ucfirst($activity->type) }}</p>
                            </div>
                            <div class="col-4">
                                <p class="mb-1"><i class="fas fa-user text-warning"></i> <strong>Penulis</strong></p>
                                <p>{{ Str::limit($activity->user->name ?? '-', 15) }}</p>
                            </div>
                        </div>

                        {{-- Stats Bar --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="fas fa-eye text-success"></i> Views: {{ $activity->views_count }}</span>
                                <span><i class="fas fa-comment text-primary"></i> Komentar: {{ $activity->comments()->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus aktivitas ini?')">
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
            {{ $activities->links() }}
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <h5>Belum ada aktivitas</h5>
            <p class="text-muted mb-3">Mulai buat aktivitas untuk gereja Anda</p>
            <a href="{{ route('activities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Aktivitas Pertama
            </a>
        </div>
    </div>
@endif

@endsection
