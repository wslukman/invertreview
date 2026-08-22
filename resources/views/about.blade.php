@extends('layouts.app')

@section('title', 'Tentang Kami - United Church')

@section('css')
<style>
    .hero-section {
        background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), url('https://images.unsplash.com/photo-1529068755536-a5ade0dcb4e8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
        color: white;
        padding: 100px 0;
        text-align: center;
        margin-top: -24px; /* Offset container padding if needed, but since it's container-fluid, maybe adjust later */
    }
    .hero-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 300;
        max-width: 800px;
        margin: 0 auto;
    }
    .vision-section {
        padding: 80px 0;
        background-color: #fff;
    }
    .vision-quote {
        font-size: 1.5rem;
        font-style: italic;
        color: var(--secondary-color);
        border-left: 5px solid var(--secondary-color);
        padding-left: 20px;
        margin: 40px 0;
    }
    .feature-card {
        text-align: center;
        padding: 40px 20px;
        height: 100%;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border-radius: 15px;
        transition: transform 0.3s;
    }
    .feature-card:hover {
        transform: translateY(-10px);
    }
    .feature-icon {
        font-size: 3rem;
        color: var(--secondary-color);
        margin-bottom: 20px;
    }
    .cta-section {
        background-color: var(--primary-color);
        color: white;
        padding: 80px 0;
        text-align: center;
    }
    .social-links-section {
        background-color: #f8f9fa;
        padding: 60px 0;
        text-align: center;
    }
    .social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin: 0 10px;
        font-size: 1.5rem;
        color: white;
        transition: transform 0.3s, box-shadow 0.3s;
        text-decoration: none;
    }
    .social-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        color: white;
    }
    .btn-youtube { background-color: #FF0000; }
    .btn-instagram { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
    .btn-tiktok { background-color: #000000; }
    .btn-blog { background-color: var(--secondary-color); }
</style>
@endsection

@section('content')
<div class="hero-section">
    <div class="container">
        <h1 class="hero-title animate__animated animate__fadeInDown">Menyatukan Tubuh Kristus, Memetakan Amanat Agung</h1>
        <p class="hero-subtitle animate__animated animate__fadeInUp animate__delay-1s">
            United Church adalah platform global untuk melacak, memvalidasi, dan melihat secara langsung penyebaran Kabar Baik ke ujung bumi.
        </p>
    </div>
</div>

<div class="vision-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <h2 class="fw-bold mb-4">Visi & Misi Kami</h2>
                <div class="vision-quote">
                    "Karena itu pergilah, jadikanlah semua bangsa murid-Ku dan baptislah mereka dalam nama Bapa dan Anak dan Roh Kudus, dan ajarlah mereka melakukan segala sesuatu yang telah Kuperintahkan kepadamu."<br>
                    <strong>(Matius 28:19-20)</strong>
                </div>
                <p class="lead text-muted">
                    Kami percaya bahwa setiap titik cahaya di peta mewakili komunitas yang telah dijangkau. Visi kami adalah memastikan tidak ada satu pun wilayah yang terlewatkan dalam pendataan Kerajaan Surga. Melalui platform ini, kita dapat melihat pergerakan Allah secara nyata, terukur, dan transparan di seluruh dunia.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <h2 class="text-center fw-bold mb-5">Apa yang Bisa Kita Lakukan Bersama?</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card">
                <i class="fas fa-globe-americas feature-icon"></i>
                <h4 class="fw-bold">Peta Global (Validasi)</h4>
                <p class="text-muted">Pendaftaran titik gereja dan komunitas sel dari seluruh penjuru dunia yang divalidasi langsung untuk keakuratan data Kerajaan.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <i class="fas fa-shoe-prints feature-icon"></i>
                <h4 class="fw-bold">Jejak Langkah</h4>
                <p class="text-muted">Melaporkan setiap ibadah, pembaptisan, dan pergerakan rohani secara real-time sebagai kesaksian nyata pergerakan injil.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <i class="fas fa-hands-helping feature-icon"></i>
                <h4 class="fw-bold">Kasih Nyata (Sosial)</h4>
                <p class="text-muted">Memantau dan berpartisipasi dalam program bantuan, pendistribusian sumber daya, dan pergerakan sosial gereja di berbagai wilayah.</p>
            </div>
        </div>
    </div>
</div>

<div class="social-links-section mt-5">
    <div class="container">
        <h3 class="fw-bold mb-4">Terhubung dengan Admin & Kreator</h3>
        <p class="text-muted mb-4">Ikuti perkembangan terbaru, update sistem, dan pelajari lebih lanjut melalui platform media sosial kami:</p>
        <div class="d-flex justify-content-center flex-wrap">
            <a href="#" target="_blank" class="social-btn btn-youtube" title="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="#" target="_blank" class="social-btn btn-instagram" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" target="_blank" class="social-btn btn-tiktok" title="TikTok">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://buku.invertreview.com" target="_blank" class="social-btn btn-blog" title="Blog / Buku Kehidupan">
                <i class="fas fa-globe"></i>
            </a>
        </div>
        <small class="d-block mt-4 text-muted"><em>*Tautan YouTube, Instagram, dan TikTok akan segera diperbarui</em></small>
    </div>
</div>

<div class="cta-section">
    <div class="container">
        <h2 class="fw-bold mb-4">Mari Menjadi Bagian dari Sejarah Besar Ini</h2>
        <p class="mb-5 lead">Daftarkan komunitas Anda hari ini dan mulailah memetakan dampak Kerajaan Surga di wilayah Anda.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('register.church') }}" class="btn btn-light btn-lg fw-bold text-primary px-4">
                <i class="fas fa-church me-2"></i> Daftarkan Gereja
            </a>
            <a href="https://buku.invertreview.com" target="_blank" class="btn btn-outline-light btn-lg fw-bold px-4">
                <i class="fas fa-book-open me-2"></i> Baca Buku Kehidupan
            </a>
        </div>
    </div>
</div>
@endsection
