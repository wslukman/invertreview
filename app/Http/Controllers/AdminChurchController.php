<?php

namespace App\Http\Controllers;

use App\Models\Church;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminChurchController extends Controller
{
    /**
     * Display list of pending churches.
     */
    public function pendingApprovals(): View
    {
        $this->authorize('view_all_churches');

        $churches = Church::pending()
            ->with('submittedBy')
            ->latest('created_at')
            ->paginate(15);

        $pendingCount = Church::pending()->count();
        $approvedCount = Church::approved()->count();
        $rejectedCount = Church::rejected()->count();

        return view('admin.churches.pending-approvals', compact('churches', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Display all churches (approved, rejected, suspended).
     */
    public function index(): View
    {
        $this->authorize('view_all_churches');

        $approvedCount = Church::where('status', 'approved')->count();
        $pendingCount = Church::where('status', 'pending')->count();
        $rejectedCount = Church::where('status', 'rejected')->count();
        $suspendedCount = Church::where('status', 'suspended')->count();
        $totalChurches = Church::count();

        $churches = Church::query()
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), fn ($q, $status) => $q->where('status', $status))
            ->with(['submittedBy', 'approvedBy'])
            ->latest('created_at')
            ->paginate(15);

        return view('admin.churches.index', compact('churches', 'totalChurches', 'approvedCount', 'pendingCount', 'rejectedCount', 'suspendedCount'));
    }

    /**
     * Show church detail.
     */
    public function show(Church $church)
    {
        // Pastikan admin punya izin
        $this->authorize('view_all_churches');

        // Eager load relasi untuk performa dan menghindari error "property does not exist"
        $church->load(['submittedBy', 'socialPrograms']);

        // Definisikan variabel $stats agar sesuai dengan kebutuhan di Blade
        $stats = [
            'members'       => $church->users()->count(), 
            'activities'    => $church->activities()->count(),
            'programs'      => $church->socialPrograms()->count(),
            'registrations' => \App\Models\ProgramRegistration::whereIn(
                                'social_program_id', 
                                $church->socialPrograms->pluck('id')
                              )->count(),
        ];

        return view('admin.churches.show', compact('church', 'stats'));
    }

    /**
     * Approve a church registration.
     */
    public function approve(Church $church)
    {
        $this->authorize('approve_church');

        // Check if already approved
        if ($church->status === 'approved') {
            return back()->withErrors(['message' => 'Gereja ini sudah di-approve sebelumnya.']);
        }

        // Update church status
        $church->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Activate the church admin user
        $user = $church->submittedBy;
        if ($user) {
            $user->update(['is_active' => true]);
        }

        // TODO: Send email notification to church admin
        // Mail::send(new ChurchApprovedNotification($church));

        return back()->with('success', "Gereja '{$church->name}' berhasil di-approve!");
    }

    /**
     * Reject a church registration.
     */
    public function reject(Church $church, Request $request)
    {
        $this->authorize('reject_church');

        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        // Check if already rejected
        if ($church->status === 'rejected') {
            return back()->withErrors(['message' => 'Gereja ini sudah ditolak sebelumnya.']);
        }

        // Update church status
        $church->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Deactivate the user
        $user = $church->submittedBy;
        if ($user) {
            $user->update(['is_active' => false]);
        }

        // TODO: Send rejection email with reason

        return back()->with('success', "Pendaftaran gereja '{$church->name}' telah ditolak.");
    }

    /**
     * Suspend a church.
     */
    public function suspend(Church $church, Request $request)
    {
        $this->authorize('suspend_church');

        $request->validate([
            'suspension_reason' => 'required|string|min:10|max:500',
        ]);

        $church->update([
            'status' => 'suspended',
        ]);

        // Deactivate all users in this church
        $church->users()->update(['is_active' => false]);

        // TODO: Send notification email

        return back()->with('success', "Gereja '{$church->name}' telah dinonaktifkan.");
    }

    /**
     * Reactivate a suspended church.
     */
    public function reactivate(Church $church)
    {
        $this->authorize('approve_church');

        if ($church->status !== 'suspended') {
            return back()->withErrors(['message' => 'Hanya gereja yang suspended yang bisa direaktifkan.']);
        }

        $church->update([
            'status' => 'approved',
        ]);

        // Reactivate users
        $church->users()->update(['is_active' => true]);

        return back()->with('success', "Gereja '{$church->name}' berhasil direaktifkan.");
    }
}
