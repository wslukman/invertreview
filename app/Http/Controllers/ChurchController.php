<?php

namespace App\Http\Controllers;

use App\Helpers\LocationHelper;
use App\Models\Church;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ChurchController extends Controller
{
    /**
     * Display a listing of the churches (Public & Admin).
     */
    public function index(Request $request)
    {
        // Tampilan Pencarian Publik (Search Form)
        if ($request->routeIs('churches.search')) {
            $churches = Church::where('status', 'approved')->latest()->limit(5)->get();
            return view('churches.search', compact('churches'));
        }

        // Tampilan Daftar Publik Default
        $churches = Church::where('status', 'approved')->latest()->paginate(9);
        return view('churches.index', compact('churches'));
    }

    /**
     * Tampilkan form registrasi gereja.
     */
    public function create(): View
    {
        return view('auth.register-church');
    }

    /**
     * Menangani pencarian gereja terdekat (POST).
     */
    public function search(Request $request)
    {
        $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90|required_without:address',
            'longitude' => 'nullable|numeric|between:-180,180|required_without:address',
            'address' => 'required_without_all:latitude,longitude|string|max:255',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $radius = $request->radius ?? 50;

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $churches = LocationHelper::findNearbyChurches($request->latitude, $request->longitude, $radius);
            return view('churches.search-results', [
                'churches' => $churches,
                'centerLat' => $request->latitude,
                'centerLon' => $request->longitude,
                'radius' => $radius,
            ]);
        }

        $churches = Church::where('status', 'approved')
            ->where('name', 'like', '%' . $request->address . '%')
            ->latest()
            ->paginate(12);

        // Menggunakan view 'search' agar hasil pencarian muncul di halaman yang memiliki form pencarian
        return view('churches.search', compact('churches'));
    }

    /**
     * Store a newly created church (Registration with Logo).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:churches,email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi Logo
            // ... tambahkan validasi lainnya (address, lat, long)
        ]);

        $church = new Church($request->except('logo'));
        $church->status = 'pending'; // Default status

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('churches/logos', 'public');
            $church->logo_path = $path;
        }

        $church->save();

        return redirect()->route('churches.index')->with('success', 'Pendaftaran gereja berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Show church detail (public).
     */
    public function show(Church $church)
    {
        $activities = $church->activities()
            ->where('is_published', true)
            ->latest()
            ->limit(4)
            ->get();

        $programs = $church->socialPrograms()
            ->where('status', 'active')
            ->latest()
            ->get();

        return view('churches.show', compact('church', 'activities', 'programs'));
    }

    /**
     * API for AJAX Nearby Search.
     */
    public function apiNearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100',
        ]);

        $radius = $request->radius ?? 50;
        $churches = LocationHelper::findNearbyChurches($request->latitude, $request->longitude, $radius);

        return response()->json([
            'success' => true,
            'data' => $churches->map(fn ($church) => [
                'id' => $church->id,
                'name' => $church->name,
                'address' => $church->address,
                'latitude' => $church->latitude,
                'longitude' => $church->longitude,
                'distance' => round($church->distance, 2),
                'distance_formatted' => LocationHelper::formatDistance($church->distance),
                'logo_url' => $church->logo_path ? asset('storage/' . $church->logo_path) : null,
                'url' => route('churches.show', $church),
            ]),
        ]);
    }
}