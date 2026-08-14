@extends('layouts.guest')

@section('title', $program->title)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('programs.public') }}">Program Sosial</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($program->title, 30) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            {{-- Header & Image --}}
            <div class="card border-0 shadow-sm overflow-hidden mb-4">
                @if($program->image_path)
                    <img src="{{ asset('storage/' . $program->image_path) }}" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-handshake text-muted fa-4x"></i>
                    </div>
                @endif
                <div class="card-body p-4">
                    <span class="badge bg-primary mb-2">{{ ucfirst(str_replace('_', ' ', $program->type)) }}</span>
                    <h1 class="h2 mb-3">{{ $program->title }}</h1>
                    
                    <div class="d-flex align-items-center text-muted mb-4">
                        <i class="fas fa-church me-2"></i> 
                        <span class="fw-bold">{{ $program->church->name }}</span>
                        <span class="mx-2">|</span>
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{-- PERBAIKAN DI SINI: Baris 33 --}}
                        {{ $program->start_date?->format('d F Y') ?? 'Tanggal segera diumumkan' }}
                    </div>

                    <h5 class="fw-bold mb-3">Deskripsi Program</h5>
                    <div class="program-description text-secondary" style="line-height: 1.8;">
                        {!! nl2br(e($program->description)) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Registration Info Card --}}
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Detail Pendaftaran</h5>
                    
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Status Kapasitas</label>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">{{ $program->registrations_count ?? 0 }} / {{ $program->capacity }} Peserta</span>
                            @php
                                $count = $program->registrations_count ?? 0;
                                $cap = $program->capacity > 0 ? $program->capacity : 1;
                                $percentage = ($count / $cap) * 100;
                            @endphp
                            <span class="text-{{ $percentage >= 100 ? 'danger' : 'success' }} fw-bold">
                                {{ $percentage >= 100 ? 'Penuh' : 'Tersedia' }}
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $percentage >= 100 ? 'bg-danger' : 'bg-success' }}" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <i class="fas fa-user-tie text-primary me-2"></i>
                        <span class="text-muted">Kontak Person:</span>
                        <p class="ms-4 mb-0 fw-bold">{{ $program->contact_person }}</p>
                    </div>

                    <div class="mb-4">
                        <i class="fas fa-phone-alt text-primary me-2"></i>
                        <span class="text-muted">Nomor WhatsApp:</span>
                        <p class="ms-4 mb-0 fw-bold">{{ $program->contact_phone }}</p>
                    </div>

                    @if($percentage < 100)
                        @auth
                            <form action="{{ route('programs.register', $program) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mb-2 shadow-sm">
                                    <i class="fas fa-edit me-2"></i> Daftar Sekarang
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill mb-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#guestRegisterModal">
                                <i class="fas fa-edit me-2"></i> Daftar Sekarang
                            </button>
                        @endauth
                    @else
                        <button class="btn btn-secondary btn-lg w-100 rounded-pill mb-2" disabled>
                            Pendaftaran Ditutup
                        </button>
                    @endif
                    
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $program->contact_phone) }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill">
                        <i class="fab fa-whatsapp me-2"></i> Tanya Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

{{-- Modal for Guest Registration --}}
@guest
<div class="modal fade" id="guestRegisterModal" tabindex="-1" aria-labelledby="guestRegisterModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('programs.register', $program) }}" method="POST">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="guestRegisterModalLabel">Pendaftaran Program (Tamu)</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p class="text-muted small mb-4">Anda belum login. Silakan isi data di bawah ini untuk mendaftar sebagai tamu, atau <a href="{{ route('login') }}">login di sini</a>.</p>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap</label>
                <input type="text" name="guest_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="guest_email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Nomor WhatsApp / HP</label>
                <input type="text" name="guest_phone" class="form-control" placeholder="Contoh: 08123456789" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Konfirmasi Pendaftaran</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endguest

@endsection