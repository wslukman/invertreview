@extends('layouts.app')

@section('title', 'Kelola Registrasi Program')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-clipboard-list"></i> Peserta Program: {{ $program->title }}</h2>
    </div>
    <div class="col-md-4 text-end">
        <form action="{{ route('programs.export', $program) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </form>
    </div>
</div>

{{-- Program Info Card --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p class="text-muted mb-1"><strong>Status Program</strong></p>
                        <p><span class="badge {{ $program->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($program->status) }}</span></p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1"><strong>Tanggal Pelaksanaan</strong></p>
                        <p>{{ $program->activity_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1"><strong>Kapasitas</strong></p>
                        <p>
                            <span class="badge bg-light text-dark">{{ $program->registered_count }}/{{ $program->capacity }}</span>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p class="text-muted mb-1"><strong>Ketersediaan</strong></p>
                        @php $remaining = $program->capacity - $program->registered_count; @endphp
                        <p>
                            <span class="badge {{ $remaining > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $remaining > 0 ? $remaining . ' Kursi Tersisa' : 'Penuh' }}
                            </span>
                        </p>
                    </div>
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
                <form method="GET" action="{{ route('programs.registrations', $program) }}" class="row g-2">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Terdaftar</option>
                            <option value="attended" {{ request('status') === 'attended' ? 'selected' : '' }}>Hadir</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama/email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Statistics --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary">{{ $stats['registered'] }}</h3>
                <p class="text-muted mb-0">Terdaftar</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success">{{ $stats['attended'] }}</h3>
                <p class="text-muted mb-0">Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-danger">{{ $stats['cancelled'] }}</h3>
                <p class="text-muted mb-0">Batal</p>
            </div>
        </div>
    </div>
</div>

{{-- Registrations Table --}}
@if($registrations->count() > 0)
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Nama</th>
                        <th width="20%">Email</th>
                        <th width="15%">Telepon</th>
                        <th width="15%">Status</th>
                        <th width="15%">Terdaftar</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $registration)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $registration->user ? $registration->user->name : $registration->guest_name }}</strong>
                            </td>
                            <td>
                                {{ $registration->user ? $registration->user->email : $registration->guest_email }}
                            </td>
                            <td>
                                {{ $registration->user ? $registration->user->phone : $registration->guest_phone }}
                            </td>
                            <td>
                                @if($registration->status === 'registered')
                                    <span class="badge bg-primary">Terdaftar</span>
                                @elseif($registration->status === 'attended')
                                    <span class="badge bg-success">Hadir</span>
                                @elseif($registration->status === 'cancelled')
                                    <span class="badge bg-danger">Batal</span>
                                @endif
                            </td>
                            <td>{{ $registration->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($registration->status === 'registered')
                                        <form action="{{ route('registrations.attendance', $registration) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Mark as attended">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($registration->status !== 'cancelled')
                                        <form action="{{ route('registrations.destroy', $registration) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete" onclick="return confirm('Hapus registrasi?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
            {{ $registrations->links() }}
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h5>Belum ada peserta terdaftar</h5>
            <p class="text-muted">Peserta akan muncul di sini ketika mereka mendaftar untuk program</p>
        </div>
    </div>
@endif

@endsection
