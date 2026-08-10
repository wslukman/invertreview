import { NextResponse } from 'next/server';
import { getAuthUser } from '@/lib/auth';
import { query } from '@/lib/db';

// 1. GET: Ambil daftar biografi yang di-publish (untuk halaman pencarian/landing)
export async function GET(req) {
  try {
    const { searchParams } = new URL(req.url);
    const search = searchParams.get('search') || '';

    let sql = `
      SELECT b.id, b.slug, b.title, b.summary, b.created_at, u.full_name as author_name 
      FROM biographies b
      JOIN users u ON b.user_id = u.id
      WHERE b.is_published = true
    `;
    const params = [];

    if (search) {
      sql += ` AND (b.title ILIKE $1 OR b.summary ILIKE $1 OR u.full_name ILIKE $1)`;
      params.push(`%${search}%`);
    }

    sql += ` ORDER BY b.created_at DESC LIMIT 20`;

    const result = await query(sql, params);
    return NextResponse.json({ biographies: result.rows });
  } catch (error) {
    console.error('Get Biographies API Error:', error);
    return NextResponse.json({ error: 'Gagal mengambil data biografi.' }, { status: 500 });
  }
}

// 2. POST: Membuat biografi baru (1 Akun = 1 Biografi)
export async function POST(req) {
  try {
    const user = await getAuthUser(req);
    if (!user) {
      return NextResponse.json({ error: 'Tidak terautentikasi.' }, { status: 401 });
    }

    const { slug, title, summary, contentMarkdown, isPublished = true } = await req.json();

    // Validasi input
    if (!slug || !title || !contentMarkdown) {
      return NextResponse.json(
        { error: 'Slug, judul, dan isi biografi (markdown) wajib diisi.' },
        { status: 400 }
      );
    }

    // Format slug agar aman (lowercase, alphanumeric & strip)
    const formattedSlug = slug.toLowerCase().trim().replace(/[^a-z0-9-_]/g, '-');

    if (formattedSlug.length < 3) {
      return NextResponse.json(
        { error: 'Slug minimal terdiri dari 3 karakter.' },
        { status: 400 }
      );
    }

    // --- ATURAN EMAS: 1 Akun = 1 Biografi ---
    // Cek apakah user_id sudah ada di database biographies
    const checkUserBio = await query('SELECT id FROM biographies WHERE user_id = $1', [user.id]);
    if (checkUserBio.rows.length > 0) {
      return NextResponse.json(
        { error: 'Anda sudah memiliki 1 data biografi. Sistem melarang kepemilikan lebih dari 1 biografi.' },
        { status: 400 }
      );
    }

    // Cek keunikan slug di database
    const checkSlug = await query('SELECT id FROM biographies WHERE slug = $1', [formattedSlug]);
    if (checkSlug.rows.length > 0) {
      return NextResponse.json(
        { error: 'Slug ini sudah digunakan oleh biografi lain. Silakan cari slug yang berbeda.' },
        { status: 400 }
      );
    }

    // Simpan biografi baru
    const result = await query(
      `INSERT INTO biographies (user_id, slug, title, summary, content_markdown, is_published)
       VALUES ($1, $2, $3, $4, $5, $6)
       RETURNING id, slug, title, summary, is_published, created_at`,
      [user.id, formattedSlug, title, summary, contentMarkdown, isPublished]
    );

    return NextResponse.json(
      {
        message: 'Biografi berhasil dibuat!',
        biography: result.rows[0],
      },
      { status: 201 }
    );
  } catch (error) {
    console.error('Create Biography API Error:', error);
    return NextResponse.json(
      { error: 'Terjadi kesalahan server saat menyimpan biografi.' },
      { status: 500 }
    );
  }
}
