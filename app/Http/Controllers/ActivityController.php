<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Church;
use App\Http\Requests\StoreActivityRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    /**
     * Tampilan daftar aktivitas untuk publik.
     */
    public function publicIndex(): View
    {
        // Menggunakan scope 'published' jika ada, atau filter manual is_published
        $activities = Activity::published()
            ->with(['church'])
            ->latest()
            ->paginate(12);

        return view('activities.index', compact('activities'));
    }

    /**
     * Detail aktivitas.
     */
    public function show(Activity $activity): View
    {
        // Proteksi jika aktivitas belum dipublikasikan
        if (!$activity->is_published && (!auth()->check() || !$this->canViewUnpublished($activity))) {
            abort(404);
        }

        // Jika ada fungsi increment views di model
        if (method_exists($activity, 'incrementViews')) {
            $activity->incrementViews();
        }

        $activity->load(['church', 'user', 'approvedComments.user']);

        return view('activities.show', compact('activity'));
    }

    /**
     * Dashboard Admin: Daftar Aktivitas (Internal)
     */
    public function manage(): View
    {
        $query = Activity::query();

        // Jika bukan super_admin, hanya lihat aktivitas milik gerejanya sendiri atau miliknya sendiri
        if (!auth()->user()->hasRole('super_admin')) {
            if (auth()->user()->hasRole('church_admin')) {
                $query->where('church_id', auth()->user()->church_id);
            } else {
                $query->where('user_id', auth()->id());
            }
        }

        $activities = $query->with(['church', 'user'])
            ->when(request('status') === 'published', fn ($q) => $q->where('is_published', true))
            ->when(request('status') === 'draft', fn ($q) => $q->where('is_published', false))
            ->when(request('search'), fn ($q) => $q->where('title', 'like', '%' . request('search') . '%'))
            ->latest()
            ->paginate(15);

        $stats = [
            'active_activities' => (clone $query)->where('is_published', true)->count(),
            'draft_activities'  => (clone $query)->where('is_published', false)->count(),
            'total_views'       => (clone $query)->sum('views_count'),
            'total_comments'    => 0, // Simplified for now, or join if needed
        ];

        return view('activities.manage', compact('activities', 'stats'));
    }

    /**
     * Form buat aktivitas baru.
     */
    public function create(): View
    {
        // Mengambil daftar gereja untuk pilihan (khusus Admin)
        $churches = Church::all();

        return view('activities.create', compact('churches'));
    }

    /**
     * Simpan aktivitas ke database.
     */
    public function store(StoreActivityRequest $request)
    {
        $validated = $request->validated();

        // Logika penentuan Church ID
        $churchId = $request->church_id 
                    ?? auth()->user()->church_id 
                    ?? Church::first()?->id;

        if (!$churchId) {
            return back()->withErrors(['church_id' => 'Gereja tidak ditemukan.'])->withInput();
        }

        // Handle Image Upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('activities', 'public');
        }

        $activity = Activity::create([
            'church_id'     => $churchId,
            'user_id'       => auth()->id(),
            'title'         => $validated['title'],
            'content'       => $validated['content'],
            'activity_date' => $validated['activity_date'],
            'type'          => $validated['type'],
            'image_path'    => $imagePath,
            'is_published'  => $request->has('is_published') ? $request->is_published : true,
        ]);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Aktivitas berhasil dibuat!');
    }

    /**
     * Form Edit.
     */
    public function edit(Activity $activity): View
    {
        // Pastikan hanya pemilik atau admin yang bisa edit
        if (!$this->canViewUnpublished($activity)) {
            abort(403);
        }

        $churches = Church::all();
        return view('activities.edit', compact('activity', 'churches'));
    }

    /**
     * Update data.
     */
    public function update(StoreActivityRequest $request, Activity $activity)
    {
        if (!$this->canViewUnpublished($activity)) {
            abort(403);
        }

        $validated = $request->validated();

        $activity->update([
            'title'         => $validated['title'],
            'content'       => $validated['content'],
            'activity_date' => $validated['activity_date'],
            'type'          => $validated['type'],
            'church_id'     => $request->church_id ?? $activity->church_id,
        ]);

        if ($request->hasFile('image')) {
            if ($activity->image_path) {
                Storage::disk('public')->delete($activity->image_path);
            }
            $activity->update([
                'image_path' => $request->file('image')->store('activities', 'public')
            ]);
        }

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Aktivitas berhasil diperbarui!');
    }

    /**
     * Hapus aktivitas.
     */
    public function destroy(Activity $activity)
    {
        if (auth()->id() !== $activity->user_id && !auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        if ($activity->image_path) {
            Storage::disk('public')->delete($activity->image_path);
        }

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Aktivitas berhasil dihapus!');
    }

    /**
     * Helper proteksi akses.
     */
    private function canViewUnpublished(Activity $activity): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        return $user->id === $activity->user_id 
            || $user->hasRole('super_admin')
            || ($user->hasRole('church_admin') && $user->church_id === $activity->church_id);
    }
}