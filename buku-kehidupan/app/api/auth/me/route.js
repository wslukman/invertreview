import { NextResponse } from 'next/server';
import { getAuthUser } from '@/lib/auth';
import { query } from '@/lib/db';

export async function GET(req) {
  try {
    const userPayload = await getAuthUser(req);
    if (!userPayload) {
      return NextResponse.json({ error: 'Tidak terautentikasi.' }, { status: 401 });
    }

    // Ambil data terbaru dari DB
    const userResult = await query(
      'SELECT id, email, full_name, role, created_at FROM users WHERE id = ?',
      [userPayload.id]
    );

    if (userResult.rows.length === 0) {
      return NextResponse.json({ error: 'User tidak ditemukan.' }, { status: 404 });
    }

    const user = userResult.rows[0];

    // Cek apakah user sudah punya biografi
    const bioResult = await query(
      'SELECT id, slug, title FROM biographies WHERE user_id = ?',
      [user.id]
    );

    const hasBiography = bioResult.rows.length > 0;
    const biography = hasBiography ? bioResult.rows[0] : null;

    return NextResponse.json({
      user: {
        id: user.id,
        email: user.email,
        fullName: user.full_name,
        role: user.role,
        createdAt: user.created_at,
        hasBiography,
        biography,
      },
    });
  } catch (error) {
    console.error('Auth Me API Error:', error);
    return NextResponse.json({ error: 'Terjadi kesalahan server.' }, { status: 500 });
  }
}

// Handler POST untuk logout (menghapus cookie)
export async function POST() {
  const response = NextResponse.json({ message: 'Logout berhasil!' });
  response.cookies.set('auth_token', '', {
    httpOnly: true,
    expires: new Date(0),
    path: '/',
  });
  return response;
}
