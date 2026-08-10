<?php

namespace App\Http\Controllers;

use App\Models\Church;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama dengan pemetaan Leaflet.
     */
    public function index(): View
    {
        // Hanya mengambil gereja yang sudah disetujui untuk ditampilkan di peta publik
        $churches = Church::where('status', 'approved')->get();

        return view('home', compact('churches'));
    }
}