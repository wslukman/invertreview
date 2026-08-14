<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSocialProgramRequest;
use App\Models\SocialProgram;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class SocialProgramController extends Controller
{
    /**
     * Tampilan Publik: Daftar Program Sosial
     */
    public function publicIndex(): View
    {
        $programs = SocialProgram::active()
            ->with('church')
            ->when(request('type'), fn ($q) => $q->where('type', request('type')))
            ->when(request('church'), fn ($q) => $q->where('church_id', request('church')))
            ->latest('start_date')
            ->paginate(12);

        // Ambil data untuk filter di Blade agar tidak error "Undefined variable"
        $churches = Church::where('status', 'approved')->get();
        $types = SocialProgram::distinct()->pluck('type');

        return view('programs.index-public', compact('programs', 'churches', 'types'));
    }

    /**
     * Tampilan Publik: Detail Program
     */
    public function publicShow(SocialProgram $program): View
    {
        if ($program->status !== 'active') {
            abort(404);
        }

        $program->load('church', 'registrations');

        return view('programs.show-public', compact('program'));
    }

    /**
     * Dashboard Admin: Daftar Program (Internal)
     */
    public function index(): View
    {
        $this->authorizeAccess('manage_programs');

        $query = SocialProgram::query();

        // Jika bukan super_admin, hanya lihat program milik gerejanya sendiri
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('church_id', auth()->user()->church_id);
        }

        $programs = $query->with('church')
            ->when(request('status'), fn ($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(15);

        return view('programs.index', compact('programs'));
    }

    /**
     * Form Buat Program Baru
     */
    public function create(): View
    {
        $this->authorizeAccess('create_program');

        return view('programs.create');
    }

    /**
     * Simpan Program Baru
     */
    public function store(StoreSocialProgramRequest $request)
    {
        $this->authorizeAccess('create_program');

        $validated = $request->validated();

        // Tentukan church_id: dari user profil atau gereja pertama (untuk super admin)
        $churchId = auth()->user()->church_id ?? Church::first()?->id;

        if (!$churchId) {
            return back()->withErrors(['error' => 'Belum ada gereja yang terdaftar di sistem.']);
        }

        $program = SocialProgram::create([
            'church_id'      => $churchId,
            'title'          => $validated['title'],
            'description'    => $validated['description'],
            'type'           => $validated['type'],
            'start_date'     => $validated['start_date'],
            'end_date'       => $validated['end_date'] ?? null,
            'capacity'       => $validated['capacity'],
            'contact_person' => $validated['contact_person'],
            'contact_phone'  => $validated['contact_phone'],
            'status'         => 'draft', // Default saat baru buat
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('programs', 'public');
            $program->update(['image_path' => $imagePath]);
        }

        return redirect()->route('programs.index')
            ->with('success', 'Program berhasil dibuat sebagai draft.');
    }

    /**
     * Form Edit Program
     */
    public function edit(SocialProgram $program): View
    {
        $this->authorizeAccess('edit_program', $program);

        return view('programs.edit', compact('program'));
    }

    /**
     * Update Data Program
     */
    public function update(StoreSocialProgramRequest $request, SocialProgram $program)
    {
        $this->authorizeAccess('edit_program', $program);

        $validated = $request->validated();
        $program->update($validated);

        if ($request->hasFile('image')) {
            if ($program->image_path) {
                Storage::disk('public')->delete($program->image_path);
            }
            $imagePath = $request->file('image')->store('programs', 'public');
            $program->update(['image_path' => $imagePath]);
        }

        return redirect()->route('programs.index')
            ->with('success', 'Program berhasil diperbarui!');
    }

    /**
     * Publikasikan Program (Draft -> Active)
     */
    public function publish(SocialProgram $program)
    {
        $this->authorizeAccess('edit_program', $program);
        $program->update(['status' => 'active']);

        return back()->with('success', 'Program sekarang aktif dan dapat dilihat publik.');
    }

    /**
     * Hapus Program
     */
    public function destroy(SocialProgram $program)
    {
        $this->authorizeAccess('delete_program', $program);

        if ($program->image_path) {
            Storage::disk('public')->delete($program->image_path);
        }

        $program->delete();

        return redirect()->route('programs.index')->with('success', 'Program telah dihapus.');
    }

    /**
     * Fungsi Otorisasi Custom
     */
    private function authorizeAccess($permission, $program = null)
    {
        $user = auth()->user();

        // 1. Super Admin punya akses penuh
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // 2. Church Admin punya akses kelola program
        if ($user->hasRole('church_admin')) {
            // 3. Jika mengedit data, pastikan program milik gerejanya sendiri
            if ($program && $user->church_id !== $program->church_id) {
                abort(403, 'Anda tidak diizinkan mengelola program gereja lain.');
            }
            return true;
        }

        abort(403, 'Anda tidak memiliki izin untuk akses ini.');
    }
}