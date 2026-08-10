# DAFTAR USER - UNITED CHURCH PLATFORM
## Testing Credentials

---

## 📋 USER CREDENTIALS

### 1. Super Admin Account (ID: 1)
```
Email    : super_admin@unitedchurch.local
Password : password
Nama     : Test Super_admin
Role     : super_admin
Status   : ✅ Aktif
```
**Akses:**
- Dashboard Admin
- Kelola Gereja (Pending/Approved)
- Lihat semua Aktivitas
- Kelola User & Roles

---

### 2. Admin Account (ID: 2)
```
Email    : admin@test.com
Password : password
Nama     : Admin Test
Role     : super_admin
Status   : ✅ Aktif
```
**Akses:**
- Dashboard Admin (sama dengan Super Admin)
- Kelola Gereja
- Kelola Approval Gereja

---

### 3. Church Admin Account (ID: 3)
```
Email    : church@test.com
Password : password
Nama     : Church Admin
Role     : church_admin
Status   : ✅ Aktif
```
**Akses:**
- Dashboard Gereja
- Buat & Kelola Aktivitas
- Buat & Kelola Program Sosial
- Lihat Pendaftaran Program
- Export Peserta Program

---

### 4. Member Account (ID: 4)
```
Email    : member@test.com
Password : password
Nama     : Member Test
Role     : member
Status   : ✅ Aktif
```
**Akses:**
- Dashboard Member Personal
- Cari Gereja
- Lihat Aktivitas
- Daftar Program Sosial
- Lihat Status Pendaftaran

---

## 🔑 Catatan Penting

1. **Semua Password**: `password`
2. **Lingkungan**: Development/Testing
3. **Database**: SQLite (local)
4. **Server**: http://127.0.0.1:8000

---

## 🧪 Testing Features by Role

| Feature | Super_Admin | Church_Admin | Member |
|---------|:-----------:|:------------:|:------:|
| Dashboard | ✅ | ✅ | ✅ |
| Kelola Gereja | ✅ | ❌ | ❌ |
| Approve Gereja | ✅ | ❌ | ❌ |
| Buat Aktivitas | ✅ | ✅ | ✅ |
| Buat Program | ✅ | ✅ | ❌ |
| Daftar Program | ✅ | ✅ | ✅ |
| Cari Gereja | ✅ | ✅ | ✅ |
| Lihat Aktivitas | ✅ | ✅ | ✅ |

---

## 🚀 Quick Start

1. **Login dengan Admin Super:**
   ```
   URL: http://127.0.0.1:8000/login
   Email: super_admin@unitedchurch.local
   Password: password
   ```

2. **Login dengan Church Admin:**
   ```
   Email: church@test.com
   Password: password
   ```

3. **Login sebagai Member:**
   ```
   Email: member@test.com
   Password: password
   ```

---

## 📊 Data Test

**Gereja yang tersedia:**
- 50+ churches dalam database
- Beragam status (active, pending, suspended)

**Aktivitas:**
- 200+ activities untuk testing
- Berbagai kategori

**Users:**
- 100+ users dari berbagai gereja
- Terpisah per role

---

Generated: March 23, 2026 | Laravel 12.55.1 | PHP 8.2.12
