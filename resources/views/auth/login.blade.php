<x-guest-layout>
    <div class="row justify-content-center" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 text-primary fw-bold">United Church</h2>
                    <h4 class="text-center mb-4">Masuk ke Akun Anda</h4>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                            <label class="form-check-label" for="remember_me">Ingat Saya</label>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
                        </div>
                        
                        <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1">
                            <span class="mx-3 text-muted">atau</span>
                            <hr class="flex-grow-1">
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <a href="{{ route('google.login') }}" class="btn btn-outline-dark btn-lg d-flex justify-content-center align-items-center">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" style="height: 20px;" class="me-2">
                                Lanjutkan dengan Google
                            </a>
                        </div>

                        <!-- Links -->
                        <div class="text-center">
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none me-2">Lupa Password?</a>
                            @endif

                            @if (Route::has('register'))
                                |
                                <a href="{{ route('register') }}" class="text-decoration-none ms-2">Daftar Akun Baru</a>
                            @endif
                        </div>

                        <!-- Register Church -->
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="mb-0">Atau daftarkan gereja Anda:</p>
                            <a href="{{ route('register.church') }}" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-church me-2"></i> Daftar Gereja
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
