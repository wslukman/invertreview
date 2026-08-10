@extends('layouts.guest')

@section('title', $church->name)

@section('content')
<div class="container-fluid" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 8px;">
    <div class="row align-items-center">
        <div class="col-md-3 text-center">
            @if($church->logo_path)
                <img src="{{ asset('storage/' . $church->logo_path) }}" alt="{{ $church->name }}" style="max-width: 150px; border-radius: 8px;">
            @else
                <div style="width: 150px; height: 150px; background: rgba(255,255,255,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                    <i class="fas fa-church fa-5x"></i>
                </div>
            @endif
        </div>
        <div class="col-md-9">
            <h1 class="mb-2">{{ $church->name }}</h1>
            <p class="mb-1"><strong>📍 Alamat:</strong> {{ $church->address }}</p>
            <p class="mb-1"><strong>📅 Berdiri:</strong> {{ $church->founded_year }}</p>
            <p class="mb-1"><strong>📞 Telepon:</strong> <a href="tel:{{ $church->phone }}" style="color: inherit;">{{ $church->phone }}</a></p>
            <p class="mb-0"><strong>📧 Email:</strong> <a href="mailto:{{ $church->email }}" style="color: inherit;">{{ $church->email }}</a></p>
        </div>
    </div>
</div>

{{-- Deskripsi --}}
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Tentang Gereja</h5>
            </div>
            <div class="card-body">
                <p>{{ $church->description }}</p>
            </div>
        </div>
    </div>

    {{-- Sidebar Info --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-phone"></i> Hubungi Kami</h5>
            </div>
            <div class="card-body">
                <a href="tel:{{ $church->phone }}" class="btn btn-success btn-lg w-100 mb-2">
                    <i class="fas fa-phone"></i> Telepon
                </a>
                <a href="mailto:{{ $church->email }}" class="btn btn-primary btn-lg w-100 mb-2">
                    <i class="fas fa-envelope"></i> Email
                </a>
                <a href="https://www.google.com/maps?q={{ $church->latitude }},{{ $church->longitude }}" target="_blank" class="btn btn-warning btn-lg w-100">
                    <i class="fas fa-map"></i> Buka di Maps
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Lokasi</h5>
            </div>
            <div class="card-body">
                <p class="small">
                    <strong>Latitude:</strong> {{ $church->latitude }}<br>
                    <strong>Longitude:</strong> {{ $church->longitude }}
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Aktivitas --}}
@if($activities->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3"><i class="fas fa-calendar-alt"></i> Aktivitas Terbaru</h3>
        </div>
        @foreach($activities as $activity)
            <div class="col-md-6 mb-3">
                <div class="card">
                    @if($activity->image_path)
                        <img src="{{ asset('storage/' . $activity->image_path) }}" class="card-img-top" alt="{{ $activity->title }}" style="height: 150px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }} mb-2">
                            {{ $activity->type === 'ibadah' ? 'Ibadah' : 'Kegiatan Sosial' }}
                        </span>
                        <h5 class="card-title">{{ $activity->title }}</h5>
                        <p class="card-text small text-muted">
                            <strong>📅</strong> {{ $activity->activity_date->format('d M Y') }}<br>
                            <strong>👤</strong> {{ $activity->user->name }}
                        </p>
                        <a href="{{ route('activities.show', $activity) }}" class="btn btn-sm btn-primary">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Program Sosial --}}
@if($programs->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-3"><i class="fas fa-handshake"></i> Program Sosial Aktif</h3>
        </div>
        @foreach($programs as $program)
            <div class="col-md-6 mb-3">
                <div class="card">
                    @if($program->image_path)
                        <img src="{{ asset('storage/' . $program->image_path) }}" class="card-img-top" alt="{{ $program->title }}" style="height: 150px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <span class="badge {{ $program->type === 'pelatihan_kerja' ? 'bg-info' : 'bg-warning' }} mb-2">
                            {{ $program->type_label }}
                        </span>
                        <h5 class="card-title">{{ $program->title }}</h5>
                        <p class="card-text small">
                            <strong>📅 Mulai:</strong> {{ $program->start_date->format('d M Y') }}<br>
                            <strong>👥 Peserta:</strong> {{ $program->registered_count }}/{{ $program->capacity }}<br>
                            <strong>Sisa Tempat:</strong> 
                            <span class="badge bg-{{ $program->available_slots > 0 ? 'success' : 'danger' }}">
                                {{ $program->available_slots }}
                            </span>
                        </p>
                        <a href="{{ route('programs.publicShow', $program) }}" class="btn btn-sm btn-primary">
                            Detail & Daftar
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
