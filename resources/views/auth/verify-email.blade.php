<x-guest-layout>
    <div class="row justify-content-center" style="min-height: 100vh; display: flex; align-items: center;">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-2 text-primary fw-bold">United Church</h2>
                    <h5 class="text-center mb-4">Verifikasi Email</h5>
                    <p class="text-center text-muted mb-4">Terima kasih telah mendaftar! Sebelum melanjutkan, silakan verifikasi email Anda dengan mengklik link yang telah kami kirimkan.</p>

                    @if (session('resent'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> Link verifikasi baru telah dikirim ke email Anda.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <p class="text-center text-muted mb-4">Jika Anda tidak menerima email, silakan klik tombol di bawah untuk mengirim ulang.</p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Kirim Ulang Email Verifikasi</button>
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
