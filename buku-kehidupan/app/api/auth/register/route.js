import { NextResponse } from 'next/server';
import bcrypt from 'bcryptjs';
import { query } from '@/lib/db';

export async function POST(req) {
  try {
    const { email, password, fullName } = await req.json();

    // Validasi input
    if (!email || !password || !fullName) {
      return NextResponse.json(
        { error: 'Email, password, dan nama lengkap wajib diisi.' },
        { status: 400 }
      );
    }

    if (password.length < 6) {
      return NextResponse.json(
        { error: 'Password minimal terdiri dari 6 karakter.' },
        { status: 400 }
      );
    }

    // Periksa apakah email sudah terdaftar
    const existingUser = await query('SELECT id FROM users WHERE email = ?', [email.toLowerCase()]);
    if (existingUser.rows.length > 0) {
      return NextResponse.json(
        { error: 'Email ini sudah terdaftar.' },
        { status: 400 }
      );
    }

    // Hash password
    const salt = await bcrypt.genSalt(10);
    const passwordHash = await bcrypt.hash(password, salt);

    // Generate UUID di sisi JavaScript (kompatibel semua versi MySQL)
    const newId = crypto.randomUUID();

    // Simpan ke database
    await query(
      `INSERT INTO users (id, email, password_hash, full_name, role)
       VALUES (?, ?, ?, ?, ?)`,
      [newId, email.toLowerCase(), passwordHash, fullName, 'user']
    );

    // Ambil data user yang baru dibuat
    const newUserResult = await query(
      'SELECT id, email, full_name, role, created_at FROM users WHERE id = ?',
      [newId]
    );

    const newUser = newUserResult.rows[0];

    return NextResponse.json(
      {
        message: 'Registrasi berhasil!',
        user: {
          id: newUser.id,
          email: newUser.email,
          fullName: newUser.full_name,
          role: newUser.role,
        },
      },
      { status: 201 }
    );
  } catch (error) {
    console.error('Registration API Error:', error);
    return NextResponse.json(
      { error: 'Terjadi kesalahan pada server saat registrasi.' },
      { status: 500 }
    );
  }
}
