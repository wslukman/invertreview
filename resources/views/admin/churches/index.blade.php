@extends('layouts.app')

@section('title', 'Kelola Gereja')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-church"></i> Kelola Semua Gereja</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.churches.pending') }}" class="btn btn-warning">
            <i class="fas fa-hourglass-half"></i> Persetujuan Menunggu
            @if($pendingCount > 0)
                <span class="badge bg-danger">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>
</div>

{{-- Statistics --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Total Gereja</h6>
                        <h2 class="mb-0 text-primary">{{ $totalChurches }}</h2>
                    </div>
                    <i class="fas fa-church fa-2x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Approved</h6>
                        <h2 class="mb-0 text-success">{{ $approvedCount }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Menunggu</h6>
                        <h2 class="mb-0 text-warning">{{ $pendingCount }}</h2>
                    </div>
                    <i class="fas fa-hourglass-half fa-2x text-warning opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0 text-muted">Ditolak</h6>
                        <h2 class="mb-0 text-danger">{{ $rejectedCount }}</h2>
                    </div>
                    <i class="fas fa-times-circle fa-2x text-danger opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.churches.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspend</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="sort" class="form-select">
                            <option value="">Urutkan</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Tertua</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Churches Table --}}
@if($churches->count() > 0)
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Nama Gereja</th>
                        <th width="15%">Admin Gereja</th>
                        <th width="15%">Email</th>
                        <th width="10%">Status</th>
                        <th width="15%">Terdaftar</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($churches as $church)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $church->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($church->address, 40) }}</small>
                            </td>
                            <td>{{ $church->submittedBy->name }}</td>
                            <td>{{ $church->email }}</td>
                            <td>
                                @if($church->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($church->status === 'pending')
                                    <span class="badge bg-warning">Menunggu</span>
                                @elseif($church->status === 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @elseif($church->status === 'suspended')
                                    <span class="badge bg-dark">Suspend</span>
                                @endif
                            </td>
                            <td>{{ $church->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.churches.show', $church) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($church->status === 'pending')
                                        <button type="button" 
                                                class="btn btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $church->id }}"
                                                title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="card-footer">
            {{ $churches->links() }}
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>Tidak ada gereja ditemukan</h5>
            <p class="text-muted">Cobalah mengubah filter atau pencarian Anda</p>
        </div>
    </div>
@endif

{{-- Modals for Quick Actions --}}
@foreach($churches as $church)
    @if($church->status === 'pending')
        <div class="modal fade" id="approveModal{{ $church->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Gereja</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.churches.approve', $church) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p>Setujui pendaftaran <strong>{{ $church->name }}</strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection
