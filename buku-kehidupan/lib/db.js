import mysql from 'mysql2/promise';

let pool;

if (!global.mysqlPool) {
  global.mysqlPool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'buku_kehidupan',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
  });
}

pool = global.mysqlPool;

/**
 * Helper untuk mengeksekusi kueri SQL MySQL dengan mudah.
 * Mengembalikan format { rows } agar kompatibel dengan seluruh API route.
 * @param {string} text
 * @param {array} params
 * @returns {Promise<{ rows: Array }>}
 */
export async function query(text, params) {
  const start = Date.now();
  try {
    const [rows] = await pool.query(text, params);
    const duration = Date.now() - start;
    console.log(`[DB Query] ${text.trim().slice(0, 100)}... took ${duration}ms`);
    // SELECT → rows adalah array; INSERT/UPDATE/DELETE → rows adalah ResultSetHeader (object)
    return { rows: Array.isArray(rows) ? rows : [rows] };
  } catch (error) {
    console.error(`[DB Error] Query gagal eksekusi: ${text}`, error);
    throw error;
  }
}

export default pool;
