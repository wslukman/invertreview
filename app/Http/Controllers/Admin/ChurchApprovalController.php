<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChurchApprovalController extends Controller
{
    // Menampilkan daftar yang perlu di-approve (Pending)
    public function index()
    {
        $churches = Church::pending()->get();
        return view('admin.churches.index', compact('churches'));
    }

    // Fungsi untuk me-Reject
    public function reject(Church $church)
    {
        $church->update([
            'status' => 'rejected',
            'is_active' => false,
            'approved_by' => Auth::id(), // Siapa yang menolak
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data gereja telah ditolak.');
    }

    // Fungsi untuk me-Approve (Opsional)
    public function approve(Church $church)
    {
        $church->update([
            'status' => 'approved',
            'is_active' => true,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Data gereja telah disetujui.');
    }
}
