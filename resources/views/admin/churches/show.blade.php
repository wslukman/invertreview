@extends('layouts.app')

@section('title', $church->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-church"></i> {{ $church->name }}</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.churches.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- Status Alert --}}
<div class="row mb-4">
    <div class="col-12">
        @if($church->status === 'approved')
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <strong>Approved</strong> - Gereja ini aktif dan terdaftar
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif($church->status === 'pending')
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-hourglass-half"></i> <strong>Menunggu Persetujuan</strong> - Proses review sedang berlangsung
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif($church->status === 'rejected')
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-times-circle"></i> <strong>Ditolak</strong> - Gereja tidak disetujui untuk terdaftar
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @elseif($church->status === 'suspended')
            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                <i class="fas fa-pause-circle"></i> <strong>Suspend</strong> - Gereja telah di-suspend
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>
</div>

<div class="row">
    {{-- Main Info --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Gereja</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        @if($church->logo_path)
                            <img src="{{ asset('storage/' . $church->logo_path) }}" 
                                 alt="Logo" 
                                 class="img-fluid rounded"
                                 style="max-height: 150px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="fas fa-church text-muted fa-3x"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <p class="mb-2">
                            <strong>Status:</strong>
                            @if($church->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($church->status === 'pending')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif($church->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @elseif($church->status === 'suspended')
                                <span class="badge bg-dark">Suspend</span>
                            @endif
                        </p>
                        <p class="mb-2">
                            <strong>Terdaftar:</strong> {{ $church->created_at->format('d M Y H:i') }}
                        </p>
                        @if($church->approved_at)
                            <p class="mb-0">
                                <strong>Disetujui:</strong> {{ $church->approved_at->format('d M Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Alamat</strong></p>
                        <p class="text-muted">{{ $church->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Denominasi</strong></p>
                        <p class="text-muted">{{ $church->denomination ?? '-' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Nomor Telepon</strong></p>
                        <p>
                            <a href="tel:{{ $church->phone }}">{{ $church->phone }}</a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Email</strong></p>
                        <p>
                            <a href="mailto:{{ $church->email }}">{{ $church->email }}</a>
                        </p>
                    </div>
                </div>

                @if($church->latitude && $church->longitude)
                    <hr>
                    <p class="mb-1"><strong>Lokasi GPS</strong></p>
                    <p class="text-muted">
                        {{ number_format($church->latitude, 6) }}, {{ number_format($church->longitude, 6) }}
                    </p>
                @endif

                @if($church->description)
                    <hr>
                    <p class="mb-1"><strong>Deskripsi</strong></p>
                    <p class="text-muted">{{ $church->description }}</p>
                @endif
            </div>
        </div>

        {{-- Admin Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user"></i> Admin Gereja</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Nama:</strong> {{ $church->submittedBy->name }}
                </p>
                <p class="mb-2">
                    <strong>Email:</strong> 
                    <a href="mailto:{{ $church->submittedBy->email }}">{{ $church->submittedBy->email }}</a>
                </p>
                <p class="mb-0">
                    <strong>Status Admin:</strong>
                    @if($church->submittedBy->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Non-aktif (Menunggu Approval Gereja)</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-primary">{{ $stats['members'] }}</h4>
                        <small class="text-muted">Anggota</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-success">{{ $stats['activities'] }}</h4>
                        <small class="text-muted">Aktivitas</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-info">{{ $stats['programs'] }}</h4>
                        <small class="text-muted">Program Sosial</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <h4 class="text-warning">{{ $stats['registrations'] }}</h4>
                        <small class="text-muted">Registrasi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Actions --}}
    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                @if($church->status === 'pending')
                    <form action="{{ route('admin.churches.approve', $church) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check-circle"></i> Setujui Gereja
                        </button>
                    </form>

                    <button type="button" 
                            class="btn btn-danger w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#rejectModal">
                        <i class="fas fa-times-circle"></i> Tolak Pendaftaran
                    </button>
                @elseif($church->status === 'approved')
                    <button type="button" 
                            class="btn btn-warning w-100 mb-2" 
                            data-bs-toggle="modal" 
                            data-bs-target="#suspendModal">
                        <i class="fas fa-pause-circle"></i> Suspend Gereja
                    </button>

                    <a href="{{ route('churches.show', $church) }}" class="btn btn-info w-100">
                        <i class="fas fa-eye"></i> Lihat di Portal
                    </a>
                @elseif($church->status === 'suspended')
                    <form action="{{ route('admin.churches.unsuspend', $church) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-circle"></i> Buka Suspend
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-question-circle"></i> Bantuan</h5>
            </div>
            <div class="card-body small">
                <p class="mb-2">
                    <strong>Persetujuan Gereja:</strong> Sebelum gereja dapat beroperasi, Anda harus menyetujui pendaftaran mereka.
                </p>
                <p class="mb-2">
                    <strong>Suspend Gereja:</strong> Gunakan opsi ini jika ada masalah dengan gereja dan perlu menangguhkan aktivitas mereka.
                </p>
                <p class="mb-0">
                    <strong>Admin Gereja:</strong> Hanya admin gereja yang terdaftar dapat mengelola konten gereja mereka.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pendaftaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.churches.reject', $church) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Alasan penolakan untuk <strong>{{ $church->name }}</strong>:</p>
                    <textarea name="rejection_reason" 
                              class="form-control" 
                              rows="4"
                              placeholder="Jelaskan alasan penolakan..."
                              required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Suspend Modal --}}
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suspend Gereja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.churches.suspend', $church) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Alasan suspend untuk <strong>{{ $church->name }}</strong>:</p>
                    <textarea name="suspension_reason" 
                              class="form-control" 
                              rows="4"
                              placeholder="Jelaskan alasan suspend..."
                              required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Suspend</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
