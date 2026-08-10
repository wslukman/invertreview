# 📋 Comprehensive Development Plan: United Church Platform

**Aplikasi:** United Church (Gereja Bersatu)  
**Tech Stack:** Laravel 12 + Breeze (Blade) + Spatie Laravel-Permission  
**Status:** Project Planning Phase  
**Last Updated:** March 2026

---

## 📌 Executive Summary

Platform web untuk koordinasi multi-gereja di Palembang yang memfasilitasi:
- ✅ Registrasi & approval gereja
- ✅ Multi-role authentication (super_admin, church_admin, member)
- ✅ Manajemen kegiatan & komentar
- ✅ Program sosial (pelatihan, sembako)
- ✅ Search gereja terdekat dengan Haversine formula
- ✅ Public contact system

**Estimasi:** 4-6 minggu development

---

## 🗄️ FASE 1: DATABASE STRUCTURE

### 1.1 Migrations & Models

#### Tabel `users`
```
- id (PK)
- name
- email (unique)
- email_verified_at
- password
- phone
- role_type (enum: super_admin, church_admin, member) [DEPRECATED - use roles table]
- church_id (FK to churches) - nullable untuk super_admin
- last_login_at
- is_active (boolean)
- timestamps
- soft_deletes
```

**Model:** `App\Models\User`
- Relations: `hasMany(Activity)`, `hasMany(Comment)`, `belongsTo(Church)`

---

#### Tabel `churches`
```
- id (PK)
- name (string, unique)
- address (text)
- latitude (decimal 10,8)
- longitude (decimal 10,8)
- phone (string)
- email (string)
- description (text)
- founded_year (year)
- status (enum: pending, approved, rejected, suspended) - default: pending
- submitted_by (FK to users)
- approved_by (FK to users) - nullable
- approved_at (timestamp) - nullable
- logo_path (string) - nullable
- cover_image_path (string) - nullable
- timestamps
- soft_deletes
```

**Model:** `App\Models\Church`
- Relations: `hasMany(User)`, `hasMany(Activity)`, `hasMany(SocialProgram)`, `belongsTo(User, 'submitted_by')`, `belongsTo(User, 'approved_by')`

---

#### Tabel `activities`
```
- id (PK)
- church_id (FK)
- user_id (FK - author/member yang posting)
- title (string)
- content (text - konten panjang)
- type (enum: ibadah, kegiatan_sosial)
- activity_date (date)
- image_path (string) - nullable
- views_count (integer) - default: 0
- is_published (boolean) - default: true
- timestamps
- soft_deletes
```

**Model:** `App\Models\Activity`
- Relations: `belongsTo(Church)`, `belongsTo(User)`, `hasMany(Comment)`, `hasMany(ActivityImage)` (jika multiple images)

---

#### Tabel `comments`
```
- id (PK)
- activity_id (FK)
- user_id (FK) - nullable (untuk guest comments)
- guest_name (string) - nullable
- guest_email (string) - nullable
- content (text)
- is_approved (boolean) - default: true (atau moderate jika perlu)
- timestamps
```

**Model:** `App\Models\Comment`
- Relations: `belongsTo(Activity)`, `belongsTo(User, optional)` (polymorphic or nullable)

---

#### Tabel `social_programs`
```
- id (PK)
- church_id (FK)
- title (string)
- description (text)
- type (enum: pelatihan_kerja, pembagian_sembako)
- start_date (date)
- end_date (date) - nullable
- capacity (integer)
- registered_count (integer) - default: 0
- status (enum: draft, active, completed, cancelled) - default: draft
- image_path (string) - nullable
- contact_person (string)
- contact_phone (string)
- timestamps
- soft_deletes
```

**Model:** `App\Models\SocialProgram`
- Relations: `belongsTo(Church)`, `hasMany(ProgramRegistration)`

---

#### Tabel `program_registrations`
```
- id (PK)
- social_program_id (FK)
- user_id (FK) - nullable (untuk guest registration)
- guest_name (string) - nullable
- guest_email (string) - nullable
- guest_phone (string) - nullable
- registered_at (timestamp)
- status (enum: registered, attended, cancelled) - default: registered
- timestamps
```

**Model:** `App\Models\ProgramRegistration`
- Relations: `belongsTo(SocialProgram)`, `belongsTo(User, optional)`

---

#### Tabel `roles` & `permissions` (via Spatie)
*Create via Spatie package - jangan manual*
- roles: super_admin, church_admin, member
- permissions: create_church, approve_church, edit_activity, delete_activity, manage_program, etc.

---

### 1.2 Model Relationships Diagram

```
User
  ├─ hasMany: Activity (sebagai author)
  ├─ hasMany: Comment (sebagai commenter)
  ├─ belongsTo: Church (church_admin/member)
  └─ hasMany: Role (via Spatie pivot)

Church
  ├─ hasMany: User (members/admin)
  ├─ hasMany: Activity
  ├─ hasMany: SocialProgram
  ├─ belongsTo: User (submitted_by)
  └─ belongsTo: User (approved_by)

Activity
  ├─ belongsTo: Church
  ├─ belongsTo: User
  ├─ hasMany: Comment
  └─ hasMany: ActivityImage (optional)

Comment
  ├─ belongsTo: Activity
  └─ belongsTo: User (optional - nullable)

SocialProgram
  ├─ belongsTo: Church
  └─ hasMany: ProgramRegistration

ProgramRegistration
  ├─ belongsTo: SocialProgram
  └─ belongsTo: User (optional - nullable)
```

---

## 🔐 FASE 2: AUTHENTICATION & APPROVAL FLOW

### 2.1 Custom Authentication Setup

#### A. Register Church (Guest Registration)
**Flow:**
1. Guest mengakses `/register-church` form
2. Input: nama gereja, alamat, lat/long, telepon, deskripsi, tahun berdiri
3. Submit → Create `Church` record dengan status `pending`
4. Create temporary `User` dengan role `church_admin` (email & password)
5. Email verification diperlukan
6. **Notifikasi ke `super_admin`:** ada gereja baru menunggu approval

**Controller:** `ChurchRegistrationController`
```php
- showRegisterForm()
- store(StoreChurchRequest) // validation ketat
- verifyEmail(EmailVerificationRequest)
```

#### B. Super Admin Approval
**Flow:**
1. Super admin login ke dashboard
2. Lihat daftar churches dengan status "pending"
3. Click "Approve" atau "Reject"
4. Jika approve → `churches.status = 'approved'`, `churches.approved_by = auth()->id()`, `churches.approved_at = now()`
5. Email notifikasi ke church_admin bahwa gereja sudah approved → bisa login sekarang

**Controller:** `SuperAdminController`
```php
- pendingChurches()
- approveChurch(Church)
- rejectChurch(Church, Request) // dengan alasan
```

#### C. Church Admin Login (POST Approval)
**Flow:**
- Hanya setelah `churches.status = 'approved'` → church_admin bisa login
- Middleware: `EnsureChurchIsApproved`

**Middleware:**
```php
// app/Http/Middleware/EnsureChurchIsApproved.php
- Check: auth()->user()->church()->status == 'approved'
- Jika tidak → redirect dengan pesan "Gereja Anda belum di-approve"
```

---

### 2.2 Breeze Configuration

*Laravel Breeze sudah include default auth scaffolding:*
- Login, Register (modified untuk church registration)
- Email verification (required)
- Password reset
- Jangan gunakan Livewire, gunakan traditional Blade + form posting

**Override views:** `resources/views/auth/`
- `login.blade.php` - untuk church_admin & member
- `register.blade.php` - untuk member only (church register terpisah)
- `register-church.blade.php` - custom untuk gereja baru

---

## 🎭 FASE 3: ROLES & PERMISSIONS (Spatie Laravel-Permission)

### 3.1 Setup Install

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 3.2 Roles Definition

#### Role 1: `super_admin`
**Permissions:**
- approve_church
- reject_church
- view_all_churches
- view_all_activities
- view_all_users
- delete_activity (any)
- delete_comment (any)
- manage_reports

**Capabilities:**
- Akses `/admin` dashboard
- Approve/reject church registrations
- View analytics across all churches
- Suspend/activate churches

---

#### Role 2: `church_admin`
**Permissions:**
- manage_church_activities
- create_activity
- edit_activity (own church only)
- delete_activity (own church only)
- manage_social_programs
- create_program
- edit_program
- delete_program
- manage_members
- view_registrations
- export_members

**Capabilities:**
- Akses church dashboard
- CRUD activities untuk gereja mereka
- CRUD social programs
- View member list
- Moderate comments

---

#### Role 3: `member`
**Permissions:**
- view_church_activities
- create_activity (post kegiatan)
- edit_activity (own posts only)
- delete_activity (own posts only)
- create_comment
- delete_comment (own comments only)
- register_program
- view_registered_programs

**Capabilities:**
- View church home page
- Create activity posts
- Comment on activities
- Register to social programs
- View own registrations

---

### 3.3 Seeding Roles & Permissions

**File:** `database/seeders/RoleAndPermissionSeeder.php`

```php
public function run()
{
    // Create permissions
    $permissions = [
        'approve_church', 'reject_church', 'view_all_churches',
        'create_activity', 'edit_activity', 'delete_activity',
        'create_program', 'edit_program', 'delete_program',
        'manage_members', 'create_comment', 'delete_comment',
        // ... more
    ];
    
    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission]);
    }
    
    // Create roles & assign permissions
    $superAdmin = Role::create(['name' => 'super_admin']);
    $superAdmin->givePermissionTo(Permission::all());
    
    $churchAdmin = Role::create(['name' => 'church_admin']);
    $churchAdmin->givePermissionTo(['manage_church_activities', ...]);
    
    $member = Role::create(['name' => 'member']);
    $member->givePermissionTo(['view_church_activities', ...]);
}
```

---

## 📝 FASE 4: ACTIVITIES & COMMENTS CRUD

### 4.1 Activity Management

#### Routes
```php
// resources/routes/web.php

// Public
Route::get('/activities', [ActivityController::class, 'public_index']);
Route::get('/activities/{activity}', [ActivityController::class, 'show']);

// Member only
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/activities', [ActivityController::class, 'store'])->middleware('can:create_activity');
    Route::get('/activities/create', [ActivityController::class, 'create']);
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->middleware('can:update,activity');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->middleware('can:delete,activity');
});
```

#### Policy: `ActivityPolicy`
```php
// app/Policies/ActivityPolicy.php

public function create(User $user): bool
{
    return $user->hasPermissionTo('create_activity') && $user->church_id !== null;
}

public function update(User $user, Activity $activity): bool
{
    return $user->id === $activity->user_id || $user->hasPermissionTo('edit_activity');
}

public function delete(User $user, Activity $activity): bool
{
    return $user->id === $activity->user_id || $user->hasPermissionTo('delete_activity');
}
```

#### Validation: `StoreActivityRequest`
```php
// app/Http/Requests/StoreActivityRequest.php

public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'content' => 'required|string|min:10|max:5000',
        'type' => 'required|in:ibadah,kegiatan_sosial',
        'activity_date' => 'required|date|after_or_equal:today',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ];
}
```

#### Controller: `ActivityController`
```php
public function store(StoreActivityRequest $request)
{
    $this->authorize('create', Activity::class);
    
    $activity = Activity::create([
        'church_id' => auth()->user()->church_id,
        'user_id' => auth()->id(),
        'title' => $request->title,
        'content' => $request->content,
        'type' => $request->type,
        'activity_date' => $request->activity_date,
        'image_path' => $request->file('image')?->store('activities', 'public'),
    ]);
    
    return redirect()->route('activities.show', $activity)->with('success', 'Aktivitas berhasil dibuat!');
}
```

---

### 4.2 Comments System

#### Routes
```php
Route::post('/activities/{activity}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('can:delete,comment');
```

#### Model Flow (Guest & Auth)
```php
// app/Http/Requests/StoreCommentRequest.php
public function rules(): array
{
    return [
        'name' => 'required_if:auth,false|string|max:255',
        'email' => 'required_if:auth,false|email|max:255',
        'content' => 'required|string|min:5|max:2000',
    ];
}

// app/Http/Controllers/CommentController.php
public function store(StoreCommentRequest $request, Activity $activity)
{
    $comment = Comment::create([
        'activity_id' => $activity->id,
        'user_id' => auth()->id() ?? null,
        'guest_name' => auth()->check() ? null : $request->name,
        'guest_email' => auth()->check() ? null : $request->email,
        'content' => $request->content,
    ]);
    
    return back()->with('success', 'Komentar Anda telah ditambahkan!');
}
```

---

## 🤝 FASE 5: SOCIAL PROGRAMS CRUD

### 5.1 Program Management

#### Routes
```php
Route::middleware(['auth', 'verified', 'can:manage_social_programs'])->prefix('programs')->group(function () {
    Route::get('/', [SocialProgramController::class, 'index'])->name('programs.index');
    Route::get('/create', [SocialProgramController::class, 'create']);
    Route::post('/', [SocialProgramController::class, 'store']);
    Route::get('/{program}/edit', [SocialProgramController::class, 'edit']);
    Route::put('/{program}', [SocialProgramController::class, 'update']);
    Route::delete('/{program}', [SocialProgramController::class, 'destroy']);
});

// Public - view available programs
Route::get('/programs', [SocialProgramController::class, 'publicIndex'])->name('programs.public');
Route::get('/programs/{program}', [SocialProgramController::class, 'publicShow'])->name('programs.publicShow');
```

#### Validation: `StoreSocialProgramRequest`
```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:20|max:3000',
        'type' => 'required|in:pelatihan_kerja,pembagian_sembako',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'capacity' => 'required|integer|min:1|max:10000',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'contact_person' => 'required|string|max:255',
        'contact_phone' => 'required|phone:ID', // gunakan validation rule custom
    ];
}
```

#### Controller
```php
public function store(StoreSocialProgramRequest $request)
{
    $program = auth()->user()->church->socialPrograms()->create([
        ...$request->validated(),
        'image_path' => $request->file('image')?->store('programs', 'public'),
        'status' => 'draft',
    ]);
    
    return redirect()->route('programs.index')->with('success', 'Program sosial berhasil dibuat!');
}
```

### 5.2 Program Registration

#### Routes
```php
Route::post('/programs/{program}/register', [ProgramRegistrationController::class, 'store'])->name('programs.register');
Route::delete('/registrations/{registration}', [ProgramRegistrationController::class, 'destroy'])->middleware('can:delete,registration');
```

#### Registration Logic
```php
public function store(Request $request, SocialProgram $program)
{
    // Check capacity
    if ($program->registered_count >= $program->capacity) {
        return back()->withErrors(['message' => 'Kapasitas program sudah penuh!']);
    }
    
    // For authenticated user
    if (auth()->check()) {
        $existing = ProgramRegistration::where('social_program_id', $program->id)
            ->where('user_id', auth()->id())
            ->first();
        
        if ($existing) {
            return back()->withErrors(['message' => 'Anda sudah terdaftar!']);
        }
        
        $registration = ProgramRegistration::create([
            'social_program_id' => $program->id,
            'user_id' => auth()->id(),
        ]);
    } else {
        // For guest
        $registration = ProgramRegistration::create([
            'social_program_id' => $program->id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
        ]);
    }
    
    $program->increment('registered_count');
    
    return back()->with('success', 'Pendaftaran berhasil!');
}
```

---

## 🗺️ FASE 6: SEARCH GEREJA TERDEKAT

### 6.1 Geolocation & Haversine Formula

#### Helper Function: `calculateDistance()`
```php
// app/Helpers/LocationHelper.php

class LocationHelper
{
    /**
     * Calculate distance between two points using Haversine formula
     * @return float distance in kilometers
     */
    public static function calculateDistance(
        float $lat1, 
        float $lon1, 
        float $lat2, 
        float $lon2
    ): float {
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * asin(sqrt($a));
        
        return $earthRadius * $c;
    }
    
    /**
     * Find nearby churches
     */
    public static function findNearbyChurches(
        float $lat, 
        float $lon, 
        float $radiusKm = 50
    ) {
        return Church::where('status', 'approved')
            ->get()
            ->map(function ($church) use ($lat, $lon) {
                $church->distance = self::calculateDistance(
                    $lat, $lon, 
                    $church->latitude, 
                    $church->longitude
                );
                return $church;
            })
            ->filter(function ($church) use ($radiusKm) {
                return $church->distance <= $radiusKm;
            })
            ->sortBy('distance');
    }
}
```

### 6.2 Search Routes & Controller

#### Routes
```php
// public routes
Route::get('/find-churches', [ChurchSearchController::class, 'showSearchForm'])->name('churches.search');
Route::post('/find-churches', [ChurchSearchController::class, 'search'])->name('churches.search.post');
Route::get('/churches/{church}', [ChurchSearchController::class, 'show'])->name('churches.show');
```

#### Controller
```php
// app/Http/Controllers/ChurchSearchController.php

public function showSearchForm()
{
    return view('churches.search');
}

public function search(Request $request)
{
    $request->validate([
        'latitude' => 'required_without:address|numeric|between:-90,90',
        'longitude' => 'required_without:address|numeric|between:-180,180',
        'address' => 'required_without_all:latitude,longitude|string|max:255',
        'radius' => 'nullable|numeric|min:1|max:100',
    ]);
    
    $radius = $request->radius ?? 50;
    
    // Jika address diberikan, gunakan geocoding (via API atau local)
    if ($request->address) {
        // TODO: Implementasi geocoding (Google Maps API atau alternatif)
        $coordinates = $this->geocodeAddress($request->address);
        $lat = $coordinates['lat'];
        $lon = $coordinates['lon'];
    } else {
        $lat = $request->latitude;
        $lon = $request->longitude;
    }
    
    $churches = LocationHelper::findNearbyChurches($lat, $lon, $radius);
    
    return view('churches.search-results', [
        'churches' => $churches,
        'centerLat' => $lat,
        'centerLon' => $lon,
        'radius' => $radius,
    ]);
}

public function show(Church $church)
{
    // Hanya tampilkan gereja yang approved
    if ($church->status !== 'approved') {
        abort(404);
    }
    
    return view('churches.show', [
        'church' => $church,
        'activities' => $church->activities()->latest()->limit(5)->get(),
        'programs' => $church->socialPrograms()->where('status', 'active')->get(),
    ]);
}
```

### 6.3 Frontend - Search Form & Results

#### View: `resources/views/churches/search.blade.php`
```blade
<form id="searchForm" method="POST" action="{{ route('churches.search.post') }}">
    @csrf
    
    <div class="form-group mb-3">
        <label>Metode Pencarian</label>
        <div>
            <input type="radio" name="method" value="geolocation" id="methodGeo" checked>
            <label for="methodGeo">Gunakan Lokasi Saya (GPS)</label>
        </div>
        <div>
            <input type="radio" name="method" value="address" id="methodAddress">
            <label for="methodAddress">Cari berdasarkan Alamat/Kota</label>
        </div>
    </div>
    
    <div id="geoFields" class="form-group mb-3">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <button type="button" class="btn btn-primary" onclick="getUserLocation()">
            📍 Dapatkan Lokasi Saya
        </button>
        <span id="locationStatus"></span>
    </div>
    
    <div id="addressFields" class="form-group mb-3" style="display:none;">
        <input type="text" name="address" placeholder="Masukkan alamat/kota/provinsi" class="form-control">
    </div>
    
    <div class="form-group mb-3">
        <label for="radius">Radius Pencarian (km)</label>
        <input type="number" name="radius" id="radius" value="50" min="1" max="100" class="form-control">
    </div>
    
    <button type="submit" class="btn btn-success">🔍 Cari Gereja Terdekat</button>
</form>

<script>
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
            document.getElementById('locationStatus').textContent = '✅ Lokasi berhasil dideteksi!';
        }, function(error) {
            alert('Gagal mendapatkan lokasi: ' + error.message);
        });
    }
}

document.querySelectorAll('input[name="method"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('geoFields').style.display = this.value === 'geolocation' ? 'block' : 'none';
        document.getElementById('addressFields').style.display = this.value === 'address' ? 'block' : 'none';
    });
});
</script>
```

#### View: `resources/views/churches/search-results.blade.php`
```blade
<h1>🏘️ Gereja Terdekat (Radius {{ $radius }} km)</h1>

@if($churches->count() > 0)
    <div class="row">
        @foreach($churches as $church)
            <div class="col-md-6 mb-4">
                <div class="card">
                    @if($church->cover_image_path)
                        <img src="{{ asset('storage/' . $church->cover_image_path) }}" class="card-img-top" alt="{{ $church->name }}">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $church->name }}</h5>
                        <p class="card-text">
                            <strong>Alamat:</strong> {{ $church->address }}<br>
                            <strong>📍 Jarak:</strong> {{ round($church->distance, 2) }} km<br>
                            <strong>Tahun Berdiri:</strong> {{ $church->founded_year }}<br>
                            <strong>Deskripsi:</strong> {{ Str::limit($church->description, 100) }}
                        </p>
                        <a href="{{ route('churches.show', $church) }}" class="btn btn-info btn-sm">Lihat Detail</a>
                        <a href="tel:{{ $church->phone }}" class="btn btn-success btn-sm">📞 Hubungi</a>
                        <a href="mailto:{{ $church->email }}" class="btn btn-primary btn-sm">✉️ Email</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Optional: Map Display -->
    <div id="searchMap" style="height: 500px; margin-top: 30px;"></div>
    <script>
        // Integrate Leaflet.js or Google Maps untuk visualisasi
        const map = L.map('searchMap').setView([{{ $centerLat }}, {{ $centerLon }}], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        
        // Add marker untuk center search
        L.circleMarker([{{ $centerLat }}, {{ $centerLon }}], {
            radius: 5,
            color: 'blue',
            fill: true,
        }).addTo(map).bindPopup('📍 Lokasi Pencarian Anda');
        
        // Add markers untuk setiap gereja
        @foreach($churches as $church)
            L.marker([{{ $church->latitude }}, {{ $church->longitude }}])
                .addTo(map)
                .bindPopup(`<strong>{{ $church->name }}</strong><br>Jarak: {{ round($church->distance, 2) }} km`);
        @endforeach
        
        // Draw circle radius
        L.circle([{{ $centerLat }}, {{ $centerLon }}], {
            radius: {{ $radius * 1000 }},
            color: 'red',
            fill: false,
            weight: 2,
            opacity: 0.5,
        }).addTo(map);
    </script>
@else
    <div class="alert alert-warning">
        Tidak ada gereja ditemukan dalam radius {{ $radius }} km dari lokasi Anda.
    </div>
@endif
```

---

## 🎨 FASE 7: UI & BLADE TEMPLATES

### 7.1 Template Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php (main layout with nav)
│   ├── guest.blade.php (public layout)
│   └── admin.blade.php (admin layout)
├── components/
│   ├── navbar.blade.php
│   ├── sidebar.blade.php
│   ├── card.blade.php
│   └── form-error.blade.php
├── auth/
│   ├── login.blade.php
│   ├── register.blade.php
│   ├── register-church.blade.php
│   └── verify-email.blade.php
├── dashboard/
│   ├── admin.blade.php (super_admin dashboard)
│   ├── church.blade.php (church_admin dashboard)
│   └── member.blade.php (member dashboard)
├── activities/
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── list.blade.php
├── programs/
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── index.blade.php
├── churches/
│   ├── search.blade.php
│   ├── search-results.blade.php
│   └── show.blade.php
└── admin/
    ├── churches/
    │   └── pending-approvals.blade.php
    └── dashboard.blade.php
```

### 7.2 Main Layout: `app.blade.php`

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - United Church</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { background-color: #2c3e50; }
        .navbar-brand { font-weight: bold; color: #fff !important; }
    </style>
    @yield('css')
</head>
<body>
    @include('components.navbar')
    
    <div class="container-fluid">
        <div class="row">
            @auth
                <div class="col-md-2">
                    @include('components.sidebar')
                </div>
                <div class="col-md-10">
                    @include('components.form-error')
                    @yield('content')
                </div>
            @else
                <div class="col-12">
                    @include('components.form-error')
                    @yield('content')
                </div>
            @endauth
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')
</body>
</html>
```

### 7.3 Navigation Component

```blade
<!-- resources/views/components/navbar.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">⛪ United Church</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('churches.search') }}">🔍 Cari Gereja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('programs.public') }}">🤝 Program Sosial</a>
                </li>
                
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="userMenu">
                            @if(auth()->user()->hasRole('super_admin'))
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">📊 Admin Dashboard</a></li>
                            @elseif(auth()->user()->hasRole('church_admin'))
                                <li><a class="dropdown-item" href="{{ route('church.dashboard') }}">🏘️ Church Dashboard</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('member.dashboard') }}">👤 My Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">🚪 Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">🔐 Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">👤 Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.church') }}">⛪ Register Gereja</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
```

---

## 📋 PHASE-BY-PHASE IMPLEMENTATION ROADMAP

### Week 1: Foundation
- [ ] Database migrations & models
- [ ] Spatie roles & permissions setup
- [ ] Breeze authentication configuration
- [ ] Custom church registration flow
- [ ] Super admin approval system

### Week 2: Core Features
- [ ] Activity CRUD + comments system
- [ ] Social programs CRUD
- [ ] Program registration logic
- [ ] Basic Blade templates

### Week 3: Location Features
- [ ] Haversine distance calculation
- [ ] Geolocation functionality
- [ ] Church search & results page
- [ ] Map integration (Leaflet)

### Week 4: Polish & Testing
- [ ] UI refinements
- [ ] Form validations
- [ ] Error handling
- [ ] Unit & feature tests
- [ ] Security checks (authorization policies)

### Week 5-6: Optional Enhancements
- [ ] Soft deletes implementation
- [ ] Image optimization
- [ ] Email notifications
- [ ] Export functionality
- [ ] Reporting features

---

## 🔧 DEVELOPMENT CHECKLIST

### Essential Packages
```bash
composer require laravel/breeze
composer require spatie/laravel-permission
composer require laravel/tinker
```

### Optional Packages
```bash
composer require spatie/laravel-query-builder          # For advanced filtering
composer require spatie/laravel-medialibrary          # For image handling
composer require barryvdh/laravel-debugbar --dev      # Development
composer require laravel/pint --dev                   # Code formatting
composer require laravel/sail --dev                   # Docker support
```

### Configuration Files to Create/Modify
- `config/permission.php` (after Spatie install)
- `.env` - Database credentials, app settings
- `phpunit.xml` - Testing configuration
- `vite.config.js` - Frontend bundling

### Environment Variables
```env
APP_NAME="United Church"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=united_church
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@unitedchurch.local

# Optional: Geocoding API (Google Maps)
GOOGLE_MAPS_API_KEY=your_key_here
```

---

## ✅ SUCCESS CRITERIA

Aplikasi dianggap "complete" jika:

1. ✅ Church registration flow berfungsi (pending → approved)
2. ✅ Multi-role authentication work correctly (3 roles)
3. ✅ Member bisa create/edit/delete activities dengan peraturan yang benar
4. ✅ Comment system berfungsi untuk guest & auth user
5. ✅ Social program CRUD lengkap + registration
6. ✅ Gereja search dengan Haversine formula akurat
7. ✅ UI responsive + user-friendly dengan Bootstrap
8. ✅ Form validations ketat di semua endpoint
9. ✅ Authorization policies berlaku di semua resource
10. ✅ No critical security vulnerabilities

---

## 📚 REFERENCE & BEST PRACTICES

- **Spatie Laravel-Permission:** https://spatie.be/docs/laravel-permission
- **Laravel Authorization Policies:** https://laravel.com/docs/authorization
- **Breeze Documentation:** https://laravel.com/docs/starter-kits
- **Haversine Formula:** Great Circle Distance formula untuk geographic calculations
- **Blade Templating:** https://laravel.com/docs/blade

---

**Document Version:** 1.0  
**Last Updated:** March 2026  
**Status:** Ready for Implementation
