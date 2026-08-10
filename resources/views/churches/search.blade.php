@extends('layouts.guest')

@section('title', 'Cari Gereja Terdekat')

@section('content')
<div class="hero-section">
    <h1><i class="fas fa-search"></i> Cari Gereja Terdekat</h1>
    <p>Temukan gereja di sekitar Anda untuk beribadah dan bergabung dalam program sosial</p>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-map-location-dot"></i> Pencarian Gereja</h5>
            </div>
            <div class="card-body">
                <form id="searchForm" method="POST" action="{{ route('churches.search.post') }}">
                    @csrf

                    {{-- Metode Pencarian --}}
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">Pilih Metode Pencarian</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="method" value="geolocation" id="methodGeo" checked>
                            <label class="form-check-label" for="methodGeo">
                                <i class="fas fa-location-crosshairs"></i> Gunakan GPS/Lokasi Saya
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="method" value="address" id="methodAddress">
                            <label class="form-check-label" for="methodAddress">
                                <i class="fas fa-map-marker-alt"></i> Cari Berdasarkan Alamat/Kota
                            </label>
                        </div>
                    </div>

                    {{-- GPS Fields --}}
                    <div id="geoFields" class="mb-4">
                        <div class="input-group mb-3">
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <button class="btn btn-primary" type="button" onclick="getUserLocation(this)">
                                <i class="fas fa-location-crosshairs"></i> Ambil Lokasi GPS Saya
                            </button>
                            <span id="locationStatus" class="form-control border-left-0" style="background-color: #f8f9fa;" readonly></span>
                        </div>
                        <div id="geoWarning" class="alert alert-info" style="display: none;">
                            <i class="fas fa-info-circle"></i> Izinkan akses lokasi ketika browser menanyakan
                        </div>
                    </div>

                    {{-- Address Fields --}}
                    <div id="addressFields" class="mb-4" style="display:none;">
                        <input type="text" name="address" placeholder="Contoh: Palembang, Kota Palembang, atau Jl. Sudirman" class="form-control" maxlength="255">
                        <small class="text-muted d-block mt-2">Fitur pencarian berdasarkan alamat masih dalam pengembangan. Silakan gunakan GPS untuk hasil terbaik.</small>
                    </div>

                    {{-- Radius --}}
                    <div class="mb-4">
                        <label for="radius" class="form-label">Jarak Pencarian</label>
                        <div class="input-group">
                            <input type="range" class="form-range" name="radius" id="radius" min="1" max="100" value="50" oninput="updateRadiusLabel()">
                            <span class="input-group-text ms-2">
                                <span id="radiusLabel">50</span> km
                            </span>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-search"></i> Cari Gereja
                    </button>
                </form>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="alert alert-info">
            <h5 class="alert-heading"><i class="fas fa-lightbulb"></i> Tips Pencarian</h5>
            <ul class="mb-0">
                <li>Klik tombol "Ambil Lokasi GPS Saya" untuk deteksi otomatis lokasi Anda</li>
                <li>Sesuaikan jarak pencarian sesuai kebutuhan (1-100 km)</li>
                <li>Akan menampilkan gereja-gereja yang sudah di-approve oleh Super Admin</li>
                <li>Klik gereja untuk melihat detail, aktivitas, dan program sosial</li>
            </ul>
        </div>

        {{-- Recent Churches --}}
        @if($churches->count() > 0)
            <div class="mt-5">
                <h4 class="mb-4"><i class="fas fa-star"></i> Gereja-Gereja Terbaru</h4>
                <div class="row">
                    @foreach($churches as $church)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                @if($church->cover_image_path)
                                    <img src="{{ asset('storage/' . $church->cover_image_path) }}" class="card-img-top" alt="{{ $church->name }}" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="fas fa-church fa-3x"></i>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">{{ $church->name }}</h5>
                                    <p class="card-text small">
                                        <strong>📍</strong> {{ Str::limit($church->address, 50) }}<br>
                                        <strong>📅</strong> Berdiri tahun {{ $church->founded_year }}
                                    </p>
                                    <a href="{{ route('churches.show', $church) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-arrow-right"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function getUserLocation(btn) {
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendeteksi...';
    document.getElementById('geoWarning').style.display = 'block';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                document.getElementById('locationStatus').value = '✅ Lokasi terdeteksi! (' + position.coords.latitude.toFixed(4) + ', ' + position.coords.longitude.toFixed(4) + ')';
                btn.innerHTML = originalText;
                btn.disabled = false;
                document.getElementById('geoWarning').style.display = 'none';
            },
            function(error) {
                alert('⚠️ Error: ' + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        );
    } else {
        alert('Geolocation tidak didukung oleh browser Anda');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

function updateRadiusLabel() {
    document.getElementById('radiusLabel').textContent = document.getElementById('radius').value;
}

document.querySelectorAll('input[name="method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('geoFields').style.display = this.value === 'geolocation' ? 'block' : 'none';
        document.getElementById('addressFields').style.display = this.value === 'address' ? 'block' : 'none';
        if (this.value === 'address') {
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }
    });
});

document.getElementById('searchForm').addEventListener('submit', function(e) {
    const method = document.querySelector('input[name="method"]:checked').value;
    const lat = document.getElementById('latitude').value;
    const lon = document.getElementById('longitude').value;
    
    if (method === 'geolocation' && (!lat || !lon)) {
        e.preventDefault();
        alert('Silakan ambil lokasi GPS Anda terlebih dahulu!');
        return false;
    }
});
</script>
@endsection
