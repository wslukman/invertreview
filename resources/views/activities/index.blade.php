@extends('layouts.guest')

@section('title', 'Daftar Aktivitas')

@section('content')
<div class="hero-section">
    <h1><i class="fas fa-calendar-alt"></i> Aktivitas Gereja</h1>
    <p>Jelajahi berbagai kegiatan ibadah dan sosial dari gereja-gereja di sekitar Anda</p>
</div>

<div class="row mb-4">
    <div class="col-md-3 ms-auto">
        @auth
            @if(auth()->user()->hasPermissionTo('create_activity'))
                <a href="{{ route('activities.create') }}" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-plus"></i> Buat Aktivitas
                </a>
            @endif
        @endauth
    </div>
</div>

@if($activities->count() > 0)
    <div class="row">
        @foreach($activities as $activity)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    {{-- Featured Image --}}
                    @if($activity->image_path)
                        <img src="{{ asset('storage/' . $activity->image_path) }}" class="card-img-top" alt="{{ $activity->title }}" style="height: 180px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                            <i class="fas fa-image text-muted fa-3x"></i>
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        {{-- Badge --}}
                        <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }} mb-2" style="width: fit-content;">
                            {{ $activity->type === 'ibadah' ? 'Ibadah' : 'Sosial' }}
                        </span>

                        {{-- Title --}}
                        <h5 class="card-title">{{ Str::limit($activity->title, 50) }}</h5>

                        {{-- Church & Date --}}
                        <p class="card-text small text-muted mb-2">
                            <strong>🏘️</strong> {{ $activity->church->name }}<br>
                            <strong>📅</strong> {{ $activity->activity_date->format('d M Y') }}<br>
                            <strong>👤</strong> {{ $activity->user->name }}
                        </p>

                        {{-- Content Preview --}}
                        <p class="card-text small flex-grow-1">
                            {{ Str::limit($activity->content, 100) }}
                        </p>

                        {{-- Stats --}}
                        <div class="small text-muted mb-3 pb-3 border-top">
                            <i class="fas fa-eye"></i> {{ $activity->views_count }} | 
                            <i class="fas fa-comment"></i> {{ $activity->comments()->where('is_approved', true)->count() }}
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $activities->links() }}
    </div>
@else
    <div class="alert alert-info text-center py-5">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <h5>Belum Ada Aktivitas</h5>
        <p>Saat ini belum ada aktivitas yang dipublikasikan.</p>
    </div>
@endif

@endsection
