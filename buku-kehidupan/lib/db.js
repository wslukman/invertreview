import { Pool } from 'pg';

let pool;

if (!global.pgPool) {
  global.pgPool = new Pool({
    connectionString: process.env.DATABASE_URL,
    // Menambahkan pengaturan SSL jika terhubung ke cloud database (seperti Neon / AWS RDS)
    ssl: process.env.DATABASE_URL && 
         !process.env.DATABASE_URL.includes('localhost') && 
         !process.env.DATABASE_URL.includes('127.0.0.1')
      ? { rejectUnauthorized: false }
      : false,
  });
}

pool = global.pgPool;

/**
 * Helper untuk mengeksekusi kueri SQL PostgreSQL dengan mudah.
 * @param {string} text 
 * @param {array} params 
 * @returns {Promise<import('pg').QueryResult>}
 */
export async function query(text, params) {
  const start = Date.now();
  try {
    const res = await pool.query(text, params);
    const duration = Date.now() - start;
    console.log(`[DB Query] ${text.trim().slice(0, 100)}... took ${duration}ms`);
    return res;
  } catch (error) {
    console.error(`[DB Error] Query gagal eksekusi: ${text}`, error);
    throw error;
  }
}

export default pool;
