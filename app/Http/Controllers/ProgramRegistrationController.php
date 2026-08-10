<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRegistrationRequest;
use App\Models\ProgramRegistration;
use App\Models\SocialProgram;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramRegistrationController extends Controller
{
    /**
     * Register to a program.
     */
    public function store(StoreProgramRegistrationRequest $request, SocialProgram $program)
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($program, $validated) {
            // Lock program for update to prevent race conditions
            $program = SocialProgram::lockForUpdate()->find($program->id);

            if ($program->status !== 'active') {
                return back()->withErrors(['message' => 'Program ini tidak sedang menerima pendaftaran.']);
            }

            if ($program->registered_count >= $program->capacity) {
                return back()->withErrors(['message' => 'Kapasitas program sudah penuh!']);
            }

            // Check if already registered
            $query = ProgramRegistration::where('social_program_id', $program->id);
            
            if (auth()->check()) {
                $query->where('user_id', auth()->id());
            } else {
                $query->where('guest_email', $validated['guest_email']);
            }

            if ($query->exists()) {
                return back()->withErrors(['message' => 'Email ini sudah terdaftar di program ini!']);
            }

            // Create registration
            ProgramRegistration::create([
                'social_program_id' => $program->id,
                'user_id' => auth()->id(),
                'guest_name' => auth()->check() ? null : $validated['guest_name'],
                'guest_email' => auth()->check() ? null : $validated['guest_email'],
                'guest_phone' => auth()->check() ? null : $validated['guest_phone'],
                'status' => 'registered',
            ]);

            // Increment registered count
            $program->increment('registered_count');

            // TODO: Dispatch Email Job here

            return back()->with('success', 'Pendaftaran berhasil! Anda akan menerima email konfirmasi.');
        });
    }

    /**
     * Cancel/delete registration.
     */
    public function destroy(ProgramRegistration $registration)
    {
        $this->authorize('delete', $registration);

        $program = $registration->program;

        $registration->delete();

        // Decrement registered count
        if ($registration->status === 'registered') {
            $program->decrement('registered_count');
        }

        return back()->with('success', 'Pendaftaran berhasil dibatalkan!');
    }

    /**
     * View registrations for a program (church admin only).
     */
    public function list(SocialProgram $program): View
    {
        $this->authorizeChurchAdmin($program);

        $registrations = $program->registrations()
            ->when(request('status'), fn ($q) => $q->byStatus(request('status')))
            ->latest()
            ->paginate(20);

        return view('programs.registrations', compact('program', 'registrations'));
    }

    /**
     * Mark attendance.
     */
    public function markAttendance(ProgramRegistration $registration)
    {
        $this->authorizeChurchAdmin($registration->program);

        $registration->update(['status' => 'attended']);

        return back()->with('success', 'Kehadiran berhasil dicatat.');
    }

    /**
     * Export registrations to CSV.
     */
    public function exportCsv(SocialProgram $program)
    {
        $this->authorizeChurchAdmin($program);

        $filename = "registrations_program_{$program->id}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($program) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nama', 'Email', 'Telepon', 'Status', 'Tanggal Daftar']);

            $program->registrations()->latest()->chunk(100, function($registrations) use ($file) {
                foreach ($registrations as $reg) {
                    fputcsv($file, [
                        $reg->participant_name,
                        $reg->participant_email,
                        $reg->participant_phone,
                        $reg->status_label,
                        $reg->created_at->format('Y-m-d H:i')
                    ]);
                }
            });
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Authorize - only church admin of the program's church.
     */
    private function authorizeChurchAdmin(SocialProgram $program)
    {
        if (!auth()->check()) {
            abort(401);
        }

        if (auth()->user()->hasRole('super_admin')) {
            return true;
        }

        if (!auth()->user()->hasRole('church_admin') || auth()->user()->church_id !== $program->church_id) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }
    }
}
