-- Setup database untuk Buku Kehidupan (Biography Wiki)
-- Database Dialek: MySQL (kompatibel dengan Hostinger)

-- 1. Tabel Akun & Autentikasi
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
);

-- Index untuk mempermudah pencarian email saat login
CREATE INDEX idx_users_email ON users(email);


-- 2. Tabel Biografi Utama (Strict 1:1 dengan Users via UNIQUE user_id)
CREATE TABLE IF NOT EXISTS biographies (
    id CHAR(36) NOT NULL,
    user_id CHAR(36) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    summary TEXT,
    content_markdown TEXT NOT NULL,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_biographies_user_id (user_id),
    UNIQUE KEY uq_biographies_slug (slug),
    CONSTRAINT fk_biographies_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Index untuk optimasi pencarian performa tinggi
CREATE INDEX idx_biographies_slug ON biographies(slug);
CREATE INDEX idx_biographies_user_id ON biographies(user_id);


-- 3. Tabel Milestone / Garis Waktu Hidup (1:N dengan Biographies)
CREATE TABLE IF NOT EXISTS life_events (
    id INT NOT NULL AUTO_INCREMENT,
    biography_id CHAR(36) NOT NULL,
    event_year INT NOT NULL,
    event_title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INT DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT fk_life_events_biography FOREIGN KEY (biography_id) REFERENCES biographies(id) ON DELETE CASCADE
);

-- Index untuk mempercepat relasi join dan pengurutan milestone (timeline)
CREATE INDEX idx_life_events_biography_id ON life_events(biography_id);
CREATE INDEX idx_life_events_timeline ON life_events(biography_id, order_index ASC, event_year ASC);
