<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate; // Import Gate untuk otorisasi

class CommentController extends Controller
{
    // ... method store() Anda yang sudah ada ...

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
