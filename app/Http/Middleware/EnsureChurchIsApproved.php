<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureChurchIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has a church
        if (auth()->check() && auth()->user()->church_id) {
            $church = auth()->user()->church;

            // If church status is not "approved", redirect to pending page
            if ($church->status !== 'approved') {
                auth()->logout();

                $message = match ($church->status) {
                    'pending' => 'Gereja Anda masih menunggu persetujuan dari Super Admin. Silakan cek email untuk informasi lebih lanjut.',
                    'rejected' => 'Pendaftaran gereja Anda telah ditolak. Silakan hubungi Super Admin untuk alasan penolakan.',
                    'suspended' => 'Akun gereja Anda telah dinonaktifkan. Silakan hubungi Super Admin.',
                    default => 'Gereja Anda tidak dapat mengakses sistem saat ini.',
                };

                return redirect()->route('login')->withErrors(['church_status' => $message]);
            }
        }

        return $next($request);
    }
}
