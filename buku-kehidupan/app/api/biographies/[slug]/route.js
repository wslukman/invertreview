import { NextResponse } from 'next/server';
import { getAuthUser } from '@/lib/auth';
import pool, { query } from '@/lib/db';

// 1. GET: Mengambil detail biografi & milestone berdasarkan slug
export async function GET(req, { params }) {
  const resolvedParams = await params;
  const { slug } = resolvedParams;

  try {
    // Ambil data biografi utama
    const bioResult = await query(
      `SELECT b.*, u.full_name as author_name, u.email as author_email
       FROM biographies b
       JOIN users u ON b.user_id = u.id
       WHERE b.slug = ?`,
      [slug]
    );

    if (bioResult.rows.length === 0) {
      return NextResponse.json({ error: 'Biografi tidak ditemukan.' }, { status: 404 });
    }

    const biography = bioResult.rows[0];
    const user = await getAuthUser(req);

    // Cek otorisasi baca (jika draft/tidak dipublish, hanya pemilik/admin yang bisa melihat)
    if (!biography.is_published) {
      if (!user || (user.id !== biography.user_id && user.role !== 'admin')) {
        return NextResponse.json({ error: 'Akses terbatas. Biografi ini belum dipublikasikan.' }, { status: 403 });
      }
    }

    // Ambil data life events (milestones) terurut
    const eventsResult = await query(
      `SELECT * FROM life_events 
       WHERE biography_id = ? 
       ORDER BY order_index ASC, event_year ASC`,
      [biography.id]
    );

    return NextResponse.json({
      biography,
      lifeEvents: eventsResult.rows,
    });
  } catch (error) {
    console.error('Get Biography Slug API Error:', error);
    return NextResponse.json({ error: 'Terjadi kesalahan server.' }, { status: 500 });
  }
}

// 2. PUT: Mengupdate data biografi & milestone (Hanya Pemilik / Admin)
export async function PUT(req, { params }) {
  const resolvedParams = await params;
  const { slug } = resolvedParams;

  // Dapatkan koneksi dari pool untuk menangani transaksi MySQL
  const connection = await pool.getConnection();

  try {
    const user = await getAuthUser(req);
    if (!user) {
      return NextResponse.json({ error: 'Tidak terautentikasi.' }, { status: 401 });
    }

    // Ambil biografi lama untuk cek hak kepemilikan
    const bioCheck = await query('SELECT id, user_id FROM biographies WHERE slug = ?', [slug]);
    if (bioCheck.rows.length === 0) {
      return NextResponse.json({ error: 'Biografi tidak ditemukan.' }, { status: 404 });
    }

    const oldBio = bioCheck.rows[0];

    // Otorisasi: Hanya pemilik (atau admin) yang boleh update
    if (oldBio.user_id !== user.id && user.role !== 'admin') {
      return NextResponse.json({ error: 'Anda tidak memiliki hak untuk mengedit biografi ini.' }, { status: 403 });
    }

    const { title, summary, contentMarkdown, isPublished, lifeEvents = [] } = await req.json();

    if (!title || !contentMarkdown) {
      return NextResponse.json({ error: 'Judul dan isi biografi wajib diisi.' }, { status: 400 });
    }

    // Jalankan TRANSAKSI SQL
    await connection.beginTransaction();

    // 1. Update Biografi
    await connection.query(
      `UPDATE biographies 
       SET title = ?, summary = ?, content_markdown = ?, is_published = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`,
      [title, summary, contentMarkdown, isPublished ? 1 : 0, oldBio.id]
    );

    // 2. Hapus semua milestone lama
    await connection.query('DELETE FROM life_events WHERE biography_id = ?', [oldBio.id]);

    // 3. Masukkan milestone baru
    if (lifeEvents && lifeEvents.length > 0) {
      const insertEventSql = `
        INSERT INTO life_events (biography_id, event_year, event_title, description, order_index)
        VALUES (?, ?, ?, ?, ?)
      `;
      for (let i = 0; i < lifeEvents.length; i++) {
        const ev = lifeEvents[i];
        await connection.query(insertEventSql, [
          oldBio.id,
          parseInt(ev.event_year) || 0,
          ev.event_title || '',
          ev.description || '',
          parseInt(ev.order_index) || i,
        ]);
      }
    }

    // Commit transaksi jika sukses semua
    await connection.commit();

    // Ambil data biografi dan events yang sudah diperbarui
    const updatedBioResult = await query(
      'SELECT * FROM biographies WHERE id = ?',
      [oldBio.id]
    );
    const finalEvents = await query(
      'SELECT * FROM life_events WHERE biography_id = ? ORDER BY order_index ASC, event_year ASC',
      [oldBio.id]
    );

    return NextResponse.json({
      message: 'Biografi berhasil diperbarui!',
      biography: updatedBioResult.rows[0],
      lifeEvents: finalEvents.rows,
    });
  } catch (error) {
    // Rollback transaksi jika terjadi error
    await connection.rollback();
    console.error('Update Biography API Error:', error);
    return NextResponse.json({ error: 'Gagal memperbarui biografi (Kesalahan Database).' }, { status: 500 });
  } finally {
    // Selalu rilis koneksi kembali ke pool
    connection.release();
  }
}

// 3. DELETE: Menghapus biografi
export async function DELETE(req, { params }) {
  const resolvedParams = await params;
  const { slug } = resolvedParams;

  try {
    const user = await getAuthUser(req);
    if (!user) {
      return NextResponse.json({ error: 'Tidak terautentikasi.' }, { status: 401 });
    }

    // Cek kepemilikan
    const bioCheck = await query('SELECT id, user_id FROM biographies WHERE slug = ?', [slug]);
    if (bioCheck.rows.length === 0) {
      return NextResponse.json({ error: 'Biografi tidak ditemukan.' }, { status: 404 });
    }

    const bio = bioCheck.rows[0];

    // Otorisasi: Pemilik atau Admin
    if (bio.user_id !== user.id && user.role !== 'admin') {
      return NextResponse.json({ error: 'Akses ditolak.' }, { status: 403 });
    }

    await query('DELETE FROM biographies WHERE id = ?', [bio.id]);

    return NextResponse.json({ message: 'Biografi berhasil dihapus.' });
  } catch (error) {
    console.error('Delete Biography API Error:', error);
    return NextResponse.json({ error: 'Gagal menghapus biografi.' }, { status: 500 });
  }
}
