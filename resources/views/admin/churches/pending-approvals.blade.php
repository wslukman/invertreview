@extends('layouts.app')

@section('title', 'Persetujuan Gereja Menunggu')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2><i class="fas fa-hourglass-half"></i> Persetujuan Gereja Menunggu</h2>
        <p class="text-muted">Tinjau dan setujui pendaftaran gereja baru</p>
    </div>
</div>

{{-- Status Summary --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <p class="mb-0 small">Menunggu Persetujuan</p>
                <h2 class="mb-0">{{ $pendingCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <p class="mb-0 small">Sudah Disetujui</p>
                <h2 class="mb-0">{{ $approvedCount }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <p class="mb-0 small">Ditolak</p>
                <h2 class="mb-0">{{ $rejectedCount }}</h2>
            </div>
        </div>
    </div>
</div>

{{-- Pending Churches List --}}
@if($churches->count() > 0)
    <div class="row">
        @foreach($churches as $church)
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                @if($church->logo_path)
                                    <img src="{{ asset('storage/' . $church->logo_path) }}" 
                                         alt="Logo" 
                                         class="img-fluid rounded"
                                         style="max-height: 120px; width: 100%; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                        <i class="fas fa-church text-muted fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-9">
                                <h5 class="mb-2">{{ $church->name }}</h5>
                                
                                <p class="small text-muted mb-1">
                                    <i class="fas fa-map-marker-alt"></i> {{ Str::limit($church->address, 60) }}
                                </p>
                                
                                @if($church->phone)
                                    <p class="small mb-1">
                                        <i class="fas fa-phone"></i> 
                                        <a href="tel:{{ $church->phone }}">{{ $church->phone }}</a>
                                    </p>
                                @endif
                                
                                @if($church->email)
                                    <p class="small mb-2">
                                        <i class="fas fa-envelope"></i> 
                                        <a href="mailto:{{ $church->email }}">{{ Str::limit($church->email, 30) }}</a>
                                    </p>
                                @endif

                                <p class="text-muted small mb-2">
                                    <strong>Diajukan oleh:</strong> {{ $church->submittedBy->name }} ({{ $church->submittedBy->email }})
                                </p>

                                <p class="text-muted small">
                                    <strong>Tanggal Pengajuan:</strong> {{ $church->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        {{-- Church Details --}}
                        <hr>
                        
                        @if($church->description)
                            <p class="small mb-3">
                                <strong>Deskripsi:</strong><br>
                                {{ Str::limit($church->description, 150) }}
                            </p>
                        @endif

                        @if($church->denomination)
                            <p class="small mb-3">
                                <strong>Denominasi:</strong> {{ $church->denomination }}
                            </p>
                        @endif

                        <p class="small mb-3">
                            <strong>Lokasi GPS:</strong> 
                            @if($church->latitude && $church->longitude)
                                {{ number_format($church->latitude, 6) }}, {{ number_format($church->longitude, 6) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="card-footer bg-light">
                        <div class="row g-2">
                            <div class="col-12">
                                <a href="{{ route('admin.churches.show', $church) }}" class="btn btn-info btn-sm w-100 mb-2">
                                    <i class="fas fa-eye"></i> Lihat Detail Lengkap
                                </a>
                            </div>

                            {{-- Approve Modal Trigger --}}
                            <div class="col-6">
                                <button type="button" 
                                        class="btn btn-success btn-sm w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#approveModal{{ $church->id }}">
                                    <i class="fas fa-check-circle"></i> Setujui
                                </button>
                            </div>

                            {{-- Reject Modal Trigger --}}
                            <div class="col-6">
                                <button type="button" 
                                        class="btn btn-danger btn-sm w-100" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $church->id }}">
                                    <i class="fas fa-times-circle"></i> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Approve Modal --}}
            <div class="modal fade" id="approveModal{{ $church->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Setujui Pendaftaran Gereja</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.churches.approve', $church) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p>Anda yakin ingin menyetujui pendaftaran <strong>{{ $church->name }}</strong>?</p>
                                <p class="text-muted small">
                                    Email notifikasi akan dikirim ke admin gereja (<strong>{{ $church->submittedBy->email }}</strong>).
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Ya, Setujui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div class="modal fade" id="rejectModal{{ $church->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pendaftaran Gereja</h5>
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
                                <small class="text-muted mt-2 d-block">
                                    Pesan ini akan dikirim ke admin gereja untuk memberitahu mereka alasan penolakan.
                                </small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Ya, Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5>Semua gereja sudah di-review!</h5>
            <p class="text-muted mb-3">Tidak ada gereja yang menunggu persetujuan saat ini</p>
            <a href="{{ route('admin.churches.index') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Lihat Semua Gereja
            </a>
        </div>
    </div>
@endif

@endsection
