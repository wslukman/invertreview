@extends('layouts.guest')

@section('title', $activity->title)

@section('content')
<div class="row">
    {{-- Main Content --}}
    <div class="col-lg-8">
        <div class="card mb-4 border-0 shadow-sm">
            {{-- Featured Image --}}
            @if($activity->image_path)
                <img src="{{ asset('storage/' . $activity->image_path) }}" class="card-img-top" alt="{{ $activity->title }}" style="max-height: 400px; object-fit: cover;">
            @endif

            <div class="card-body p-4">
                {{-- Title & Meta --}}
                <div class="mb-4 pb-3 border-bottom">
                    <span class="badge {{ $activity->type === 'ibadah' ? 'bg-primary' : 'bg-success' }} mb-2 text-uppercase">
                        {{ $activity->type === 'ibadah' ? 'Kegiatan Ibadah' : 'Kegiatan Sosial' }}
                    </span>
                    <h1 class="mb-3 fw-bold">{{ $activity->title }}</h1>
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span>
                            <i class="fas fa-user-circle me-1"></i> {{ $activity->user->name }}<br>
                            <i class="fas fa-church me-1"></i> {{ $activity->church->name }}
                        </span>
                        <span class="text-end">
                            <i class="fas fa-calendar-alt me-1"></i> {{ $activity->activity_date->format('d M Y') }}<br>
                            <i class="fas fa-eye me-1"></i> {{ $activity->views_count }} kali dilihat
                        </span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="mb-4 text-secondary" style="line-height: 1.8; font-size: 1.05rem;">
                    {!! nl2br(e($activity->content)) !!}
                </div>

                {{-- Action Buttons --}}
                @auth
                    @if(auth()->user()->id === $activity->user_id || auth()->user()->hasRole('super_admin') || (auth()->user()->hasRole('church_admin') && auth()->user()->church_id === $activity->church_id))
                        <div class="d-flex gap-2 pt-3 border-top">
                            <a href="{{ route('activities.edit', $activity) }}" class="btn btn-warning rounded-pill px-4">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('activities.destroy', $activity) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill px-4" onclick="return confirm('Yakin ingin hapus aktivitas ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>

        {{-- Comments Section --}}
        <div class="card border-0 shadow-sm" id="comments">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-comments text-primary me-2"></i> 
                    Diskusi ({{ $activity->comments->where('is_approved', 1)->count() }})
                </h5>
            </div>

            <div class="card-body p-4">
                {{-- List Komentar --}}
                @forelse($activity->comments->where('is_approved', 1) as $comment)
                    <div class="mb-4 p-3 bg-light rounded-3 position-relative shadow-sm border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    {{ strtoupper(substr($comment->user ? $comment->user->name : $comment->guest_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">
                                        {{ $comment->user ? $comment->user->name : $comment->guest_name }}
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <p class="mt-3 mb-0 text-secondary" style="font-style: italic;">"{{ $comment->content }}"</p>

                        @auth
                            @if(auth()->user()->id === $comment->user_id || auth()->user()->hasRole('super_admin'))
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
</form>
<form method="POST" action="{{ route('comments.destroy', $comment) }}" class="mt-2 text-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" onclick="return confirm('Hapus komentar ini?')">
                                        <small><i class="fas fa-trash-alt"></i> Hapus</small>
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-comment-dots fa-3x text-light mb-3"></i>
                        <p class="text-muted">Belum ada diskusi. Jadilah yang pertama!</p>
                    </div>
                @endforelse

                {{-- Comment Form --}}
                <div class="mt-5 pt-4 border-top">
                    @auth
                        <h6 class="fw-bold mb-3"><i class="fas fa-pen me-2"></i> Tambahkan Komentar</h6>
                        <form method="POST" action="{{ route('comments.store') }}">
                            @csrf
                            {{-- PENTING: ID Aktivitas dikirim via hidden input sesuai update Controller --}}
                            <input type="hidden" name="activity_id" value="{{ $activity->id }}">

                            <div class="mb-3">
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3" placeholder="Tulis komentar atau doa Anda..." required></textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Komentar
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info rounded-3 border-0 shadow-sm text-center">
                            <i class="fas fa-info-circle me-2"></i> Silakan <strong><a href="{{ route('login') }}" class="text-primary">Login</a></strong> untuk ikut berdiskusi.
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="fas fa-church text-primary me-2"></i> Penyelenggara
            </div>
            <div class="card-body text-center">
                @if($activity->church->logo_path)
                    <img src="{{ asset('storage/' . $activity->church->logo_path) }}" alt="{{ $activity->church->name }}" class="mb-3 shadow-sm rounded-circle border p-1" style="width: 80px; height: 80px; object-fit: cover;">
                @else
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px;">
                        <i class="fas fa-church fa-2x text-muted"></i>
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $activity->church->name }}</h5>
                <p class="small text-muted mb-3">{{ Str::limit($activity->church->address, 100) }}</p>
                <a href="{{ route('churches.show', $activity->church) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill">
                    Lihat Profil Gereja
                </a>
            </div>
        </div>
    </div>
</div>
@endsection