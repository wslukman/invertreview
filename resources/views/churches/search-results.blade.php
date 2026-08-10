@extends('layouts.guest')

@section('title', 'Hasil Pencarian Gereja')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">
                <i class="fas fa-map-location-dot"></i> 
                Gereja Terdekat
                <span class="badge bg-primary">{{ $churches->count() }} gereja ditemukan</span>
            </h2>
            <p class="text-muted">Radius pencarian: <strong>{{ $radius }} km</strong> | Lokasi: <strong>{{ $centerLat }}, {{ $centerLon }}</strong></p>
        </div>
    </div>

    @if($churches->count() > 0)
        <div class="row">
            {{-- Map Column --}}
            <div class="col-lg-6 mb-4">
                <div id="searchMap" class="card" style="height: 500px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    {{-- Map akan dirender di sini --}}
                </div>
            </div>

            {{-- List Column --}}
            <div class="col-lg-6">
                <div class="row">
                    @foreach($churches as $church)
                        <div class="col-12 mb-3">
                            <div class="card h-100 church-card" data-lat="{{ $church->latitude }}" data-lon="{{ $church->longitude }}">
                                <div class="row g-0">
                                    @if($church->cover_image_path)
                                        <div class="col-md-4">
                                            <img src="{{ asset('storage/' . $church->cover_image_path) }}" class="img-fluid rounded-start" alt="{{ $church->name }}" style="height: 120px; object-fit: cover;">
                                        </div>
                                    @else
                                        <div class="col-md-4 bg-secondary d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class="fas fa-church fa-2x text-white"></i>
                                        </div>
                                    @endif
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $church->name }}</h5>
                                            <p class="card-text small mb-2">
                                                <strong>📍 Jarak:</strong> 
                                                <span class="badge bg-success">{{ round($church->distance, 2) }} km</span>
                                            </p>
                                            <p class="card-text small mb-2">
                                                <strong>📅</strong> Berdiri: {{ $church->founded_year }}<br>
                                                <strong>📞</strong> {{ $church->phone }}
                                            </p>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('churches.show', $church) }}" class="btn btn-primary" title="Lihat Detail">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                                <a href="tel:{{ $church->phone }}" class="btn btn-success" title="Hubungi">
                                                    <i class="fas fa-phone"></i>
                                                </a>
                                                <a href="mailto:{{ $church->email }}" class="btn btn-info" title="Email">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                                <a href="https://www.google.com/maps?q={{ $church->latitude }},{{ $church->longitude }}" target="_blank" class="btn btn-warning" title="Buka di Maps">
                                                    <i class="fas fa-map"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Map Script --}}
        <script>
            // Initialize map
            const map = L.map('searchMap').setView([{{ $centerLat }}, {{ $centerLon }}], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Add center marker
            L.marker([{{ $centerLat }}, {{ $centerLon }}], {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    shadowSize: [41, 41]
                })
            }).addTo(map).bindPopup('<strong>📍 Lokasi Pencarian Anda</strong>');

            // Add churches markers
            @foreach($churches as $church)
                L.marker([{{ $church->latitude }}, {{ $church->longitude }}], {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        shadowSize: [41, 41]
                    })
                }).addTo(map).bindPopup(`
                    <div style="min-width: 200px;">
                        <strong>{{ $church->name }}</strong><br>
                        <small>Jarak: {{ round($church->distance, 2) }} km</small><br>
                        <a href="{{ route('churches.show', $church) }}" class="btn btn-sm btn-primary mt-2">Lihat Detail</a>
                    </div>
                `, {maxWidth: 250});
            @endforeach

            // Draw radius circle
            L.circle([{{ $centerLat }}, {{ $centerLon }}], {
                radius: {{ $radius * 1000 }},
                color: '#3498db',
                fill: true,
                fillColor: '#3498db',
                fillOpacity: 0.1,
                weight: 2,
                opacity: 0.5
            }).addTo(map);

            // Interactive highlighting
            document.querySelectorAll('.church-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 4px 16px rgba(52, 152, 219, 0.4)';
                    this.style.transform = 'translateY(-2px)';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                    this.style.transform = 'translateY(0)';
                });
            });
        </script>
    @else
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="alert alert-warning">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Tidak Ada Gereja Ditemukan</h4>
                    <p class="mb-0">
                        Maaf, kami tidak menemukan gereja dalam radius {{ $radius }} km dari lokasi Anda. 
                        Coba perluas jarak pencarian atau gunakan alamat berbeda.
                    </p>
                    <hr>
                    <a href="{{ route('churches.search') }}" class="btn btn-warning mt-2">
                        <i class="fas fa-redo"></i> Coba Lagi
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    #searchMap {
        border-radius: 8px !important;
    }
</style>
@endsection
