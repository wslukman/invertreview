<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Church;
use App\Models\SocialProgram;
use App\Models\Comment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display super_admin dashboard.
     */
    public function adminDashboard(): View
    {
        // Only super admin
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $stats = [
            'total_churches' => Church::count(),
            'approved_churches' => Church::where('status', 'approved')->count(),
            'pending_churches' => Church::where('status', 'pending')->count(),
            'total_activities' => Activity::count(),
            'total_programs' => SocialProgram::count(),
            'total_comments' => Comment::count(),
        ];

        $recentChurches = Church::pending()
            ->with('submittedBy')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentActivities = Activity::published()
            ->with('church', 'user')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentChurches', 'recentActivities'));
    }

    /**
     * Display church_admin dashboard.
     */
    public function churchDashboard(): View
    {
        // Must have church_admin role
        if (!auth()->user()->hasRole('church_admin')) {
            abort(403);
        }

        $church = auth()->user()->church;

        if (!$church) {
            abort(404, 'Gereja tidak ditemukan.');
        }

        $stats = [
            'total_members' => $church->users()->count(),
            'total_activities' => $church->activities()->count(),
            'total_programs' => $church->socialPrograms()->count(),
            'total_comments' => Comment::whereIn('activity_id', $church->activities()->pluck('id'))->count(),
            'upcoming_programs' => $church->socialPrograms()->upcoming()->count(),
            'total_registrations' => $church->socialPrograms()
                ->sum('registered_count'),
        ];

        $recentActivities = $church->activities()
            ->with('user', 'comments')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentPrograms = $church->socialPrograms()
            ->latest('created_at')
            ->limit(5)
            ->get();

        $activePrograms = $church->socialPrograms()
            ->where('status', 'active')
            ->latest('start_date')
            ->limit(3)
            ->get();

        return view('dashboard.church', compact('church', 'stats', 'recentActivities', 'recentPrograms', 'activePrograms'));
    }

    /**
     * Display member dashboard.
     */
    public function memberDashboard(): View
{
    $user = auth()->user();
    $church = $user->church;

    // 1. Ambil data pendaftaran (untuk tabel di bawah)
    $myRegistrations = $user->programRegistrations()
        ->with(['program.church'])
        ->latest()
        ->get();

    // 2. Siapkan Statistik (Pastikan array key-nya sama dengan Blade)
    $stats = [
        'my_activities' => $user->activities()->count(),
        'my_comments' => $user->comments()->count(),
        'registered_programs' => $myRegistrations->count(), // Ini yang tadi bikin error baris 52
    ];

    // 3. Ambil Aktivitas Saya
    $myActivities = $user->activities()
        ->latest()
        ->limit(5)
        ->get();

    // 4. Ambil Feed Aktivitas (Aktivitas terbaru dari gereja-gereja)
    // Ini diperlukan untuk baris 168 di Blade
    $activityFeed = Activity::with(['church'])
        ->withCount('comments')
        ->latest()
        ->limit(10)
        ->get();

    return view('dashboard.member', compact(
        'user', 
        'church', 
        'stats', 
        'myActivities', 
        'myRegistrations', 
        'activityFeed' // Tambahkan ini agar tidak error di baris 168
    ));
}
}