<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // Import Gate untuk otorisasi

class CommentController extends Controller
{
    /**
     * Menyimpan komentar baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'content' => 'required|string|max:1000',
        ]);

        $comment = new Comment();
        $comment->activity_id = $validated['activity_id'];
        $comment->content = $validated['content'];
        $comment->is_approved = true; // Auto-approve untuk sekarang

        if (auth()->check()) {
            $comment->user_id = auth()->id();
        } else {
            // Jika fitur komentar guest diaktifkan di kemudian hari
            $comment->guest_name = 'Guest';
        }

        $comment->save();

        return back()->with('success', 'Komentar berhasil dikirim.');
    }

    /**
     * Menghapus komentar dari database.
     */
    public function destroy(Comment $comment)
    {
        // 1. Otorisasi: Mengecek Policy (app/Policies/CommentPolicy.php)
        // Method 'delete' di Policy akan otomatis dipanggil
        Gate::authorize('delete', $comment);

        // 2. Proses Hapus
        $comment->delete();

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
