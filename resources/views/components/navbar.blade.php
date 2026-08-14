<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background-color: #2c3e50;">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-church"></i> United Church
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                {{-- Public Links --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">Tentang Kami</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('churches.search') }}">Cari Gereja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('programs.public') }}">Program Sosial</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning fw-bold" href="https://buku.invertreview.com" target="_blank">
                        <i class="fas fa-book-open"></i> Buku Kehidupan
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            
                            @if(auth()->user()->hasRole('super_admin'))
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.churches.pending') }}">Persetujuan Gereja</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.churches.index') }}">Semua Gereja</a></li>
                            @endif

                            @if(auth()->user()->hasAnyRole(['church_admin', 'member']))
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('activities.create') }}">Buat Aktivitas</a></li>
                            @endif

                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register.church') }}">Daftar Gereja</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>