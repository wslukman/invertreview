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
    const existingUser = await query('SELECT id FROM users WHERE email = $1', [email.toLowerCase()]);
    if (existingUser.rows.length > 0) {
      return NextResponse.json(
        { error: 'Email ini sudah terdaftar.' },
        { status: 400 }
      );
    }

    // Hash password
    const salt = await bcrypt.genSalt(10);
    const passwordHash = await bcrypt.hash(password, salt);

    // Simpan ke database
    const newUser = await query(
      `INSERT INTO users (email, password_hash, full_name, role)
       VALUES ($1, $2, $3, $4)
       RETURNING id, email, full_name, role, created_at`,
      [email.toLowerCase(), passwordHash, fullName, 'user']
    );

    return NextResponse.json(
      {
        message: 'Registrasi berhasil!',
        user: {
          id: newUser.rows[0].id,
          email: newUser.rows[0].email,
          fullName: newUser.rows[0].full_name,
          role: newUser.rows[0].role,
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
