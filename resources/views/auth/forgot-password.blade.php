<x-guest-layout>
    <div class="row justify-content-center" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-2 text-primary fw-bold">United Church</h2>
                    <h5 class="text-center mb-4">Lupa Password?</h5>
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    @if (session('status'))
                        <div class="alert alert-success text-center border-0 bg-light-success p-4 rounded-3">
                            <i class="fas fa-paper-plane fa-3x mb-3 text-success"></i>
                            <h5 class="fw-bold text-success mb-2">Link Berhasil Dikirim!</h5>
                            <p class="text-muted mb-4">{{ session('status') }}<br>Silakan periksa kotak masuk atau folder spam Anda.</p>
                            
                            <a href="https://mail.google.com/" target="_blank" class="btn btn-success btn-lg w-100 mb-3 shadow-sm">
                                <i class="fab fa-google me-2"></i> Buka Kotak Masuk Gmail
                            </a>
                            <a href="https://mail.yahoo.com/" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                Buka Yahoo Mail
                            </a>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold"><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</a>
                        </div>
                    @else
                        <p class="text-center text-muted mb-4">Masukkan email Anda dan kami akan mengirimkan link untuk membuat password baru.</p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Alamat Email</label>
                                <input id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    <i class="fas fa-key me-2"></i> Kirim Link Reset Password
                                </button>
                            </div>

                            <!-- Back Link -->
                            <div class="text-center mt-4">
                                <a href="{{ route('login') }}" class="text-decoration-none text-muted"><i class="fas fa-arrow-left me-1"></i> Kembali ke halaman masuk</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
