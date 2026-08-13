<x-guest-layout>
    <div class="row justify-content-center" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-2 text-primary fw-bold">United Church</h2>
                    <h5 class="text-center mb-4">Buku Kehidupan (Verifikasi Email)</h5>
                    <p class="text-center text-muted mb-4">Terima kasih telah mendaftar! Sebelum mulai menggunakan aplikasi, silakan verifikasi email Anda.</p>
                    
                    <div class="alert alert-info text-center border-0 bg-light p-4 rounded-3 mb-4">
                        <i class="fas fa-envelope-open-text fa-3x mb-3 text-primary"></i>
                        <h5 class="fw-bold text-primary mb-2">Periksa Email Anda</h5>
                        <p class="text-muted mb-4">Kami telah mengirimkan tautan verifikasi ke email yang Anda daftarkan.</p>
                        
                        <a href="https://mail.google.com/" target="_blank" class="btn btn-primary btn-lg w-100 mb-3 shadow-sm">
                            <i class="fab fa-google me-2"></i> Buka Kotak Masuk Gmail
                        </a>
                        <a href="https://mail.yahoo.com/" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                            Buka Yahoo Mail
                        </a>
                    </div>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-1"></i> Tautan verifikasi baru telah dikirimkan!
                        </div>
                    @endif

                    <p class="text-center text-muted small mb-3">Tidak menerima email? Periksa folder spam atau minta tautan baru di bawah ini.</p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary fw-bold">
                                <i class="fas fa-paper-plane me-2"></i> Kirim Ulang Tautan Verifikasi
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-secondary">Logout</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
