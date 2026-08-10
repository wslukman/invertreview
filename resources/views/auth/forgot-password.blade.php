<x-guest-layout>
    <div class="row justify-content-center" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-2 text-primary fw-bold">United Church</h2>
                    <h5 class="text-center mb-4">Lupa Password?</h5>
                    <p class="text-center text-muted mb-4">Masukkan email Anda dan kami akan mengirimkan link untuk reset password.</p>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Kirim Link Reset Password</button>
                        </div>

                        <!-- Back Link -->
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none">Kembali ke Login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
