-- Setup database untuk Buku Kehidupan (Biography Wiki)
-- Database Dialek: PostgreSQL
-- Skema ini mencakup tabel akun, biografi utama, garis waktu hidup (milestone), dan index optimasi.

-- Aktifkan pgcrypto untuk UUID generator gen_random_uuid() jika belum aktif
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- 1. Tabel Akun & Autentikasi
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user', -- 'user' / 'admin'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk mempermudah pencarian email saat login
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);


-- 2. Tabel Biografi Utama (Strict 1:1 dengan Users via UNIQUE user_id)
CREATE TABLE IF NOT EXISTS biographies (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID UNIQUE NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content_markdown TEXT NOT NULL,
    is_published BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index Unik Otomatis dibuat untuk kolom slug karena didefinisikan UNIQUE.
-- Tambahan index eksplisit untuk optimasi pencarian performa tinggi slug:
CREATE UNIQUE INDEX IF NOT EXISTS idx_biographies_slug ON biographies(slug);

-- Index untuk pencarian relasi 1:1 ke user_id
CREATE INDEX IF NOT EXISTS idx_biographies_user_id ON biographies(user_id);


-- 3. Tabel Milestone / Garis Waktu Hidup (1:N dengan Biographies)
CREATE TABLE IF NOT EXISTS life_events (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    biography_id UUID NOT NULL REFERENCES biographies(id) ON DELETE CASCADE,
    event_year INT NOT NULL,
    event_title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INT DEFAULT 0
);

-- Index untuk mempercepat relasi join dan pengurutan milestone (timeline)
CREATE INDEX IF NOT EXISTS idx_life_events_biography_id ON life_events(biography_id);
CREATE INDEX IF NOT EXISTS idx_life_events_timeline ON life_events(biography_id, order_index ASC, event_year ASC);
