<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'United Church') }} - Platform Koordinasi Multi-Gereja</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

        <style>
            * {
                font-family: 'Poppins', sans-serif;
            }
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                overflow-x: hidden;
            }
            .navbar {
                background-color: rgba(255, 255, 255, 0.95);
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .navbar .nav-link {
                color: #333 !important;
                font-weight: 500;
                margin: 0 10px;
            }
            .navbar .nav-link:hover {
                color: #667eea !important;
            }
            .navbar .btn {
                margin-left: 10px;
            }
            .hero {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 100px 0;
                text-align: center;
                margin-top: 70px;
            }
            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 20px;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            }
            .hero p {
                font-size: 1.3rem;
                margin-bottom: 30px;
                opacity: 0.95;
            }
            .btn-hero {
                background-color: white;
                color: #667eea;
                font-weight: 600;
                padding: 12px 40px;
                border-radius: 50px;
                border: none;
                transition: all 0.3s;
                margin: 10px;
            }
            .btn-hero:hover {
                background-color: #f0f0f0;
                transform: translateY(-2px);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                color: #764ba2;
            }
            .btn-hero-outline {
                background-color: transparent;
                color: white;
                border: 2px solid white;
                font-weight: 600;
                padding: 10px 38px;
                border-radius: 50px;
                transition: all 0.3s;
                margin: 10px;
            }
            .btn-hero-outline:hover {
                background-color: white;
                color: #667eea;
                transform: translateY(-2px);
            }
            .feature-card {
                background: white;
                border-radius: 15px;
                padding: 30px;
                text-align: center;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
                transition: all 0.3s;
                margin-bottom: 30px;
                height: 100%;
            }
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            }
            .feature-card i {
                font-size: 3rem;
                color: #667eea;
                margin-bottom: 20px;
            }
            .feature-card h5 {
                color: #333;
                font-weight: 600;
                margin-bottom: 15px;
            }
            .feature-card p {
                color: #666;
                line-height: 1.6;
            }
            .section-title {
                font-size: 2.5rem;
                font-weight: 700;
                color: #333;
                text-align: center;
                margin-bottom: 50px;
                position: relative;
                padding-bottom: 20px;
            }
            .section-title::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 60px;
                height: 4px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
                border-radius: 2px;
            }
            .features, .process, .stats, .faq {
                padding: 80px 0;
                background-color: #f8f9fa;
            }
            .features {
                background-color: white;
            }
            .process-card {
                text-align: center;
                margin: 20px 0;
            }
            .process-number {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0 auto 20px;
            }
            .process-card h5 {
                color: #333;
                font-weight: 600;
                margin-bottom: 15px;
            }
            .process-card p {
                color: #666;
            }
            .stat-box {
                background: white;
                padding: 30px;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            }
            .stat-number {
                font-size: 2.5rem;
                font-weight: 700;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                margin-bottom: 10px;
            }
            .stat-label {
                color: #666;
                font-weight: 500;
            }
            .faq-item {
                margin-bottom: 20px;
            }
            .accordion-button {
                font-weight: 600;
                color: #333;
                padding: 15px 20px;
            }
            .accordion-button:not(.collapsed) {
                background-color: #f0f0f0;
                color: #667eea;
            }
            .accordion-body {
                color: #666;
                line-height: 1.8;
            }
            footer {
                background-color: #1a1a1a;
                color: white;
                padding: 50px 0 20px;
                margin-top: 50px;
            }
            footer p {
                margin-bottom: 5px;
            }
            .cta-section {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 80px 0;
                text-align: center;
            }
            .cta-section h2 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 20px;
            }
            .cta-section p {
                font-size: 1.1rem;
                margin-bottom: 30px;
                opacity: 0.95;
            }
        </style>
    </head>
    <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">
                    <i class="fas fa-church" style="color: #667eea;"></i> United Church
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Fitur</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faq">FAQ</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/dashboard') }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('login') }}">Masuk</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-sm btn-primary" href="{{ route('register') }}">
                                    <i class="fas fa-plus"></i> Daftar Gereja
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>Platform Koordinasi Multi-Gereja</h1>
                <p>Hubungkan, Kelola, dan Kembangkan Komunitas Gereja Anda Bersama Kami</p>
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-hero">
                            <i class="fas fa-tachometer-alt"></i> Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-hero">
                            <i class="fas fa-plus-circle"></i> Daftar Gratis
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-hero-outline">
                            <i class="fas fa-sign-in-alt"></i> Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features" id="features">
            <div class="container">
                <h2 class="section-title">Fitur Unggulan</h2>
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-map-location-dot"></i>
                            <h5>Temukan Gereja</h5>
                            <p>Cari gereja terdekat dengan jangkauan hingga 100 km menggunakan GPS dan filter lokasi</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-calendar-check"></i>
                            <h5>Kelola Aktivitas</h5>
                            <p>Buat, kelola, dan bagikan aktivitas gereja dengan mudah kepada anggota jemaat</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-handshake"></i>
                            <h5>Program Sosial Gratis</h5>
                            <p>Selenggarakan program praktis seperti pelatihan, pemberian makanan, dan kesehatan</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-users"></i>
                            <h5>Komunitas Kuat</h5>
                            <p>Bangun jaringan anggota gereja dengan tools kolaborasi dan komunikasi terpadu</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-comments"></i>
                            <h5>Diskusi Terbuka</h5>
                            <p>Fasilitasi percakapan bermakna dengan sistem komentar yang moderat dan terorganisir</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="feature-card">
                            <i class="fas fa-lock"></i>
                            <h5>Aman & Terpercaya</h5>
                            <p>Platform dengan enkripsi tingkat tinggi dan persetujuan admin untuk setiap gereja</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cross Promo Buku Kehidupan -->
        <section class="buku-kehidupan-promo py-5" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); border-top: 1px solid #e9ecef; border-bottom: 1px solid #e9ecef;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-center text-lg-start mb-4 mb-lg-0">
                        <h3 class="fw-bold" style="color: #2c3e50;"><i class="fas fa-book-open" style="color: #667eea;"></i> Abadikan Perjalanan Hidup Anda</h3>
                        <p class="lead mb-0 text-muted">Ingin mengabadikan kisah kesaksian, perjalanan rohani, atau biografi tokoh gereja? Gunakan <strong>Buku Kehidupan</strong>, platform gratis kami untuk menulis perjalanan hidup yang menginspirasi.</p>
                    </div>
                    <div class="col-lg-4 text-center text-lg-end">
                        <a href="https://buku.invertreview.com" target="_blank" class="btn btn-lg btn-primary shadow-sm" style="background-color: #667eea; border: none; border-radius: 50px; padding: 12px 30px;">
                            <i class="fas fa-pencil-alt"></i> Tulis Biografi Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Section -->
        <section class="process">
            <div class="container">
                <h2 class="section-title">Cara Memulai</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="process-card">
                            <div class="process-number">1</div>
                            <h5>Daftar Gereja</h5>
                            <p>Isi data gereja Anda dan tunggu persetujuan dari admin. Proses cepat dan mudah!</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-card">
                            <div class="process-number">2</div>
                            <h5>Undang Anggota</h5>
                            <p>Ajak anggota jemaat untuk bergabung dan kelola struktur organisasi gereja</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="process-card">
                            <div class="process-number">3</div>
                            <h5>Mulai Berkolaborasi</h5>
                            <p>Buat aktivitas, program sosial, dan kelola dengan efisien bersama tim gereja</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="stats">
            <div class="container">
                <h2 class="section-title">Statistik Platform</h2>
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-box">
                            <div class="stat-number">50+</div>
                            <p class="stat-label">Gereja Terdaftar</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-box">
                            <div class="stat-number">45</div>
                            <p class="stat-label">Gereja Disetujui</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-box">
                            <div class="stat-number">150+</div>
                            <p class="stat-label">Aktivitas Aktif</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stat-box">
                            <div class="stat-number">80+</div>
                            <p class="stat-label">Program Sosial</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq" id="faq">
            <div class="container">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Apakah platform ini gratis?
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Ya, platform United Church sepenuhnya gratis untuk semua gereja. Kami berkomitmen untuk membantu gereja berkembang tanpa biaya tambahan.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Berapa lama waktu persetujuan gereja?
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Persetujuan gereja biasanya memakan waktu 1-3 hari kerja. Admin kami akan meninjau data gereja Anda dan memberikan status update melalui email.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Bagaimana cara mengundang anggota gereja?
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Admin gereja dapat membuat akun untuk anggota atau membagikan tautan undangan. Setiap anggota dapat mendaftar dengan email dan password mereka sendiri.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Apakah data saya aman?
                                    </button>
                                </h2>
                                <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Keamanan adalah prioritas kami. Semua data dienkripsi dan disimpan di server yang aman. Hanya admin gereja yang dapat mengakses informasi sensitif.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Apa itu program sosial?
                                    </button>
                                </h2>
                                <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Program sosial adalah inisiatif gereja yang dapat dikelola di platform ini, seperti pelatihan keterampilan, pemberian makanan, program kesehatan, pendidikan, dan lainnya.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item faq-item">
                                <h2 class="accordion-header" id="faq6">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
                                        <i class="fas fa-question-circle me-2" style="color: #667eea;"></i> Bagaimana jika saya butuh bantuan?
                                    </button>
                                </h2>
                                <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Tim support kami siap membantu melalui email dan chat. Hubungi kami kapan saja dan kami akan merespons dalam waktu 24 jam.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Siap Memulai Perjalanan Anda?</h2>
                <p>Bergabunglah dengan ribuan gereja yang sudah mempercayai United Church</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-right"></i> Ke Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus-circle"></i> Daftar Gereja Sekarang
                    </a>
                @endauth
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <h5><i class="fas fa-church"></i> United Church</h5>
                        <p>Platform koordinasi multi-gereja untuk Palembang dan sekitarnya.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Menu</h5>
                        <ul class="list-unstyled">
                            <li><a href="/" class="text-white-50 text-decoration-none">Beranda</a></li>
                            <li><a href="#features" class="text-white-50 text-decoration-none">Fitur</a></li>
                            <li><a href="#faq" class="text-white-50 text-decoration-none">FAQ</a></li>
                            <li class="mt-2"><a href="https://buku.invertreview.com" target="_blank" class="text-warning text-decoration-none"><i class="fas fa-book-open"></i> Buku Kehidupan</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 mb-4">
                        <h5>Hubungi Kami</h5>
                        <p class="text-white-50"><i class="fas fa-envelope"></i> wslukman@gmail.com</p>
                        <p class="text-white-50"><i class="fas fa-phone"></i> +62 711-XXX-XXXX</p>
                    </div>
                </div>
                <hr style="border-color: rgba(255,255,255,0.1);">
                <div class="text-center text-white-50">
                    <p>&copy; {{ date('Y') }} United Church. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Smooth Scroll -->
        <script>
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    let target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        </script>
    </body>
</html>
