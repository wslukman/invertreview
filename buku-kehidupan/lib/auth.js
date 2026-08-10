import jwt from 'jsonwebtoken';

const JWT_SECRET = process.env.JWT_SECRET || 'super_secret_jwt_key_buku_kehidupan_2026';

/**
 * Signs a payload to generate JWT token.
 * @param {object} payload 
 * @returns {string} token
 */
export function signToken(payload) {
  return jwt.sign(payload, JWT_SECRET, { expiresIn: '7d' });
}

/**
 * Verifies JWT token.
 * @param {string} token 
 * @returns {object|null} verified payload or null
 */
export function verifyToken(token) {
  try {
    return jwt.verify(token, JWT_SECRET);
  } catch (error) {
    return null;
  }
}

/**
 * Extracts and verifies token from Request headers or cookies.
 * Supports App Router request contexts.
 * @param {Request} req
 * @returns {Promise<object|null>} The user payload if verified, or null
 */
export async function getAuthUser(req) {
  let token = null;

  // 1. Try to get from Authorization header
  const authHeader = req.headers.get('authorization');
  if (authHeader && authHeader.startsWith('Bearer ')) {
    token = authHeader.split(' ')[1];
  }

  // 2. Try to get from cookies (Next.js Request Cookie)
  if (!token) {
    try {
      const cookieHeader = req.headers.get('cookie');
      if (cookieHeader) {
        const cookies = cookieHeader.split(';').reduce((acc, cookie) => {
          const [key, value] = cookie.trim().split('=');
          acc[key] = value;
          return acc;
        }, {});
        token = cookies['auth_token'];
      }
    } catch (e) {
      console.error("Error reading cookies from headers", e);
    }
  }

  if (!token) return null;

  return verifyToken(token);
}
