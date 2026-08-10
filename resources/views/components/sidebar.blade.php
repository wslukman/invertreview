<aside class="sidebar">
    <div class="px-3 py-2">
        <h5 class="mb-3">
            <i class="fas fa-compass"></i> Menu
        </h5>

        {{-- Super Admin Menu --}}
        @if(auth()->user()->hasRole('super_admin'))
            <nav class="nav flex-column">
                <a class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link {{ Route::is('admin.churches.pending') ? 'active' : '' }}" href="{{ route('admin.churches.pending') }}">
                    <i class="fas fa-hourglass-half"></i> Persetujuan
                    <span class="badge bg-warning ms-2">{{ \App\Models\Church::pending()->count() }}</span>
                </a>
                <a class="nav-link {{ Route::is('admin.churches.index') ? 'active' : '' }}" href="{{ route('admin.churches.index') }}">
                    <i class="fas fa-list"></i> Semua Gereja
                </a>
            </nav>
        @endif

        {{-- Church Admin Menu --}}
        @if(auth()->user()->hasRole('church_admin'))
            <nav class="nav flex-column">
                <a class="nav-link {{ Route::is('church.dashboard') ? 'active' : '' }}" href="{{ route('church.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                <hr class="my-2">

                <span class="small text-muted px-3 d-block mb-2">AKTIVITAS</span>
                <a class="nav-link {{ Route::is('activities.create') ? 'active' : '' }}" href="{{ route('activities.create') }}">
                    <i class="fas fa-plus"></i> Buat Aktivitas
                </a>
                <a class="nav-link" href="{{ route('activities.index') }}">
                    <i class="fas fa-list"></i> Daftar Aktivitas
                </a>

                <hr class="my-2">

                <span class="small text-muted px-3 d-block mb-2">PROGRAM SOSIAL</span>
                <a class="nav-link {{ Route::is('programs.index') ? 'active' : '' }}" href="{{ route('programs.index') }}">
                    <i class="fas fa-list"></i> Program
                </a>
                <a class="nav-link {{ Route::is('programs.create') ? 'active' : '' }}" href="{{ route('programs.create') }}">
                    <i class="fas fa-plus"></i> Buat Program
                </a>

                <hr class="my-2">

                <span class="small text-muted px-3 d-block mb-2">LAINNYA</span>
                <a class="nav-link" href="{{ route('churches.search') }}">
                    <i class="fas fa-map"></i> Cari Gereja
                </a>
                <a class="nav-link" href="{{ route('programs.public') }}">
                    <i class="fas fa-handshake"></i> Program Publik
                </a>
            </nav>
        @endif

        {{-- Member Menu --}}
        @if(auth()->user()->hasRole('member'))
            <nav class="nav flex-column">
                <a class="nav-link {{ Route::is('member.dashboard') ? 'active' : '' }}" href="{{ route('member.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>

                <hr class="my-2">

                <span class="small text-muted px-3 d-block mb-2">AKTIVITAS</span>
                <a class="nav-link {{ Route::is('activities.create') ? 'active' : '' }}" href="{{ route('activities.create') }}">
                    <i class="fas fa-plus"></i> Buat Aktivitas
                </a>
                <a class="nav-link" href="{{ route('activities.index') }}">
                    <i class="fas fa-list"></i> Daftar Aktivitas
                </a>

                <hr class="my-2">

                <span class="small text-muted px-3 d-block mb-2">PENEMUAN</span>
                <a class="nav-link" href="{{ route('churches.search') }}">
                    <i class="fas fa-map"></i> Cari Gereja
                </a>
                <a class="nav-link" href="{{ route('programs.public') }}">
                    <i class="fas fa-handshake"></i> Program Sosial
                </a>
            </nav>
        @endif
    </div>
</aside>
