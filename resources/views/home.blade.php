@extends('layouts.app')

@section('title', 'Pemetaan Global Amanat Agung')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    #map { height: 600px; width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05); }
    .custom-popup .leaflet-popup-content-wrapper { border-radius: 8px; padding: 5px; }
    .popup-img { width: 100%; height: 120px; object-fit: cover; border-radius: 5px; margin-top: 8px; }
    .status-badge { font-size: 10px; padding: 2px 6px; border-radius: 10px; text-transform: uppercase; font-weight: bold; display: inline-block; }
    .vision-text { font-size: 1.1rem; color: #555; max-width: 800px; }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary mb-3"><i class="fas fa-globe-asia"></i> Peta Global Amanat Agung</h2>
        <p class="vision-text mx-auto">
            Memvisualisasikan dan memvalidasi pergerakan pemberitaan Injil ke seluruh bangsa-bangsa. 
            Setiap titik mewakili komunitas orang percaya yang siap melayani masyarakat.
        </p>
        <span class="badge bg-success fs-6 mt-2"><i class="fas fa-check-circle"></i> {{ $churches->count() }} Titik Terjangkau</span>
    </div>

    <div id="map"></div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    // Inisialisasi Peta (Global View / Skala Dunia)
    const map = L.map('map').setView([20, 0], 2);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Cluster Group untuk performa ratusan ribu data
    const markers = L.markerClusterGroup();

    // Data dari Laravel
    const churches = @json($churches);

    churches.forEach(church => {
        if (church.latitude && church.longitude) {
            const marker = L.marker([church.latitude, church.longitude]);
            
            // URL Detail Gereja (Mengarah ke Unit Terkait)
            const detailUrl = `{{ url('/churches') }}/${church.id}`;
            const logo = church.logo_path ? `{{ asset('storage') }}/${church.logo_path}` : 'https://via.placeholder.com/150?text=No+Image';

            const popupContent = `
                <div class="custom-popup" style="width: 200px;">
                    <strong style="font-size:14px; display:block; margin-bottom:2px;">${church.name}</strong>
                    <span class="status-badge bg-success text-white">Terverifikasi</span>
                    <img src="${logo}" class="popup-img">
                    <p class="text-muted small mt-2">${church.address.substring(0, 50)}...</p>
                    <div class="d-grid gap-2 mt-2">
                        <a href="${detailUrl}" class="btn btn-primary btn-sm text-white">Lihat Profil Unit</a>
                        <button onclick="window.open('https://www.google.com/maps/dir/?api=1&destination=${church.latitude},${church.longitude}')" 
                                class="btn btn-outline-secondary btn-sm">
                            Petunjuk Arah
                        </button>
                    </div>
                </div>
            `;
            marker.bindPopup(popupContent);
            markers.addLayer(marker);
        }
    });

    map.addLayer(markers);
</script>
@endsection