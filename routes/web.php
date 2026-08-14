<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChurchController;
use App\Http\Controllers\SocialProgramController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\Auth\RegisterChurchController;
use App\Http\Controllers\AdminChurchController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes - United Church
|--------------------------------------------------------------------------
*/

// --- 1. GUEST / PUBLIC ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('welcome');
Route::get('/home', fn() => redirect()->route('dashboard'))->name('home');
Route::view('/about', 'about')->name('about');

Route::get('/debug-proxy', function () {
    return [
        'isSecure' => request()->isSecure(),
        'clientIp' => request()->getClientIp(),
        'x-forwarded-proto' => request()->header('X-Forwarded-Proto'),
        'scheme' => request()->getScheme(),
        'url' => url('/'),
    ];
});

// Navbar Public Links
Route::get('/activities', [ActivityController::class, 'publicIndex'])->name('activities.index');
Route::get('/programs', [SocialProgramController::class, 'publicIndex'])->name('programs.public');
Route::get('/churches', [ChurchController::class, 'index'])->name('churches.index');
Route::get('/churches/search', [ChurchController::class, 'index'])->name('churches.search');
Route::get('/register-church', [RegisterChurchController::class, 'showForm'])->name('register.church');
Route::post('/register-church', [RegisterChurchController::class, 'store'])->name('register.church.store');

// Public Program Registration
Route::post('/programs/{program}/register', [ProgramRegistrationController::class, 'store'])->name('programs.register');

// --- 2. AUTHENTICATED ROUTES ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Redirector (Logic diperjelas)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('member.dashboard');
    })->name('dashboard');

    Route::get('/member-dashboard', [DashboardController::class, 'memberDashboard'])->name('member.dashboard');
    Route::get('/church-dashboard', [DashboardController::class, 'churchDashboard'])->name('church.dashboard');

    // --- INTERACTION (Comments) ---
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // --- PROGRAM REGISTRATIONS (Public Action for Authenticated) ---
    Route::delete('/registrations/{registration}', [ProgramRegistrationController::class, 'destroy'])->name('programs.registrations.destroy');

    // --- MANAGEMENT (Church Admin & Super Admin) ---
    Route::middleware(['role:church_admin|super_admin'])->group(function () {
        // Activity Management
        Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        
        // Social Program Management
        Route::get('/manage-programs', [SocialProgramController::class, 'index'])->name('programs.index');
        Route::get('/programs/create', [SocialProgramController::class, 'create'])->name('programs.create');
        Route::post('/programs', [SocialProgramController::class, 'store'])->name('programs.store');
        Route::get('/programs/{program}/edit', [SocialProgramController::class, 'edit'])->name('programs.edit');
        Route::put('/programs/{program}', [SocialProgramController::class, 'update'])->name('programs.update');
        Route::patch('/programs/{program}/publish', [SocialProgramController::class, 'publish'])->name('programs.publish');
        Route::delete('/programs/{program}', [SocialProgramController::class, 'destroy'])->name('programs.destroy');

        // Registration Management (Internal)
        Route::controller(ProgramRegistrationController::class)->group(function() {
            Route::get('/programs/{program}/registrations', 'list')->name('programs.registrations.list');
            Route::post('/registrations/{registration}/attend', 'markAttendance')->name('programs.registrations.attend');
            Route::get('/programs/{program}/export', 'exportCsv')->name('programs.registrations.export');
        });
    });

    // --- PROFILE ---
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.show');
        Route::get('/profile/edit', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// --- 3. SUPER ADMIN ONLY ---
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/users', [ProfileController::class, 'index'])->name('users.index');
    
    // Church Management untuk Admin
    Route::controller(AdminChurchController::class)->prefix('churches')->as('churches.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/pending', 'pendingApprovals')->name('pending');
        Route::get('/{church}', 'show')->name('show');
        Route::post('/{church}/approve', 'approve')->name('approve');
        Route::post('/{church}/reject', 'reject')->name('reject');
        Route::post('/{church}/suspend', 'suspend')->name('suspend');
        Route::post('/{church}/unsuspend', 'reactivate')->name('unsuspend');
    });
});

// --- 4. DYNAMIC DETAIL ROUTES (Ditaruh paling bawah) ---
Route::post('/churches/search', [ChurchController::class, 'search'])->name('churches.search.post');
Route::get('/api/churches/nearby', [ChurchController::class, 'apiNearby'])->name('api.churches.nearby');
Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/programs/{program}', [SocialProgramController::class, 'publicShow'])->name('programs.publicShow');
Route::get('/churches/{church}', [ChurchController::class, 'show'])->name('churches.show');

require __DIR__.'/auth.php';