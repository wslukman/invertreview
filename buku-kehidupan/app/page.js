'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function LandingPage() {
  const [biographies, setBiographies] = useState([]);
  const [search, setSearch] = useState('');
  const [loading, setLoading] = useState(true);
  const [user, setUser] = useState(null);

  // Ambil user login dan daftar biografi
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const res = await fetch('/api/auth/me');
        if (res.ok) {
          const data = await res.json();
          setUser(data.user);
        }
      } catch (e) {
        // Abaikan jika tidak login
      }
    };

    fetchUser();
    fetchBiographies();
  }, []);

  const fetchBiographies = async (searchQuery = '') => {
    setLoading(true);
    try {
      const url = searchQuery 
        ? `/api/biographies?search=${encodeURIComponent(searchQuery)}`
        : '/api/biographies';
      const res = await fetch(url);
      if (res.ok) {
        const data = await res.json();
        setBiographies(data.biographies);
      }
    } catch (e) {
      console.error('Error fetching biographies:', e);
    } finally {
      setLoading(false);
    }
  };

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    fetchBiographies(search);
  };

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col font-sans">
      {/* Header / Navbar */}
      <header className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <span className="text-2xl">📖</span>
            <Link href="/" className="text-xl font-bold text-gray-900 tracking-tight hover:text-blue-600 transition-colors">
              Buku Kehidupan
            </Link>
          </div>
          <nav className="flex items-center space-x-4">
            {user ? (
              <div className="flex items-center space-x-4">
                <span className="text-sm text-gray-600 hidden sm:inline">Halo, <strong className="text-gray-900">{user.fullName}</strong></span>
                <Link
                  href="/editor"
                  className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow"
                >
                  Dashboard Editor
                </Link>
              </div>
            ) : (
              <Link
                href="/auth"
                className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow"
              >
                Mulai Menulis
              </Link>
            )}
          </nav>
        </div>
      </header>

      {/* Hero Section */}
      <section className="bg-white py-16 border-b border-gray-100">
        <div className="max-w-4xl mx-auto text-center px-4">
          <h1 className="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight">
            Abadikan Perjalanan Hidup Anda di <span className="text-blue-600">Buku Kehidupan</span>
          </h1>
          <p className="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">
            Platform Biography Wiki publik. Bagikan riwayat hidup, pengalaman, pencapaian, dan timeline hidup Anda kepada dunia secara terstruktur dan estetik.
          </p>

          {/* Search Bar */}
          <form onSubmit={handleSearchSubmit} className="mt-8 max-w-xl mx-auto flex gap-2">
            <input
              type="text"
              placeholder="Cari nama tokoh atau biografi..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="flex-grow px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-900 transition-all shadow-inner"
            />
            <button
              type="submit"
              className="bg-gray-900 hover:bg-gray-800 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow cursor-pointer"
            >
              Cari
            </button>
          </form>
        </div>
      </section>

      {/* Main List Section */}
      <main className="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 w-full">
        <h2 className="text-2xl font-bold text-gray-900 mb-8 flex items-center">
          <span className="mr-2">✨</span> Biografi Publik Terbaru
        </h2>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map((i) => (
              <div key={i} className="animate-pulse bg-white p-6 rounded-2xl border border-gray-200 space-y-4">
                <div className="h-4 bg-gray-200 rounded w-1/3" />
                <div className="h-6 bg-gray-200 rounded w-3/4" />
                <div className="space-y-2">
                  <div className="h-4 bg-gray-200 rounded" />
                  <div className="h-4 bg-gray-200 rounded w-5/6" />
                </div>
                <div className="h-4 bg-gray-200 rounded w-1/4" />
              </div>
            ))}
          </div>
        ) : biographies.length === 0 ? (
          <div className="text-center py-16 bg-white rounded-2xl border border-gray-200 p-8">
            <span className="text-4xl block mb-4">📭</span>
            <p className="text-gray-500 font-medium">Tidak ada biografi publik yang ditemukan.</p>
            {search && (
              <button 
                onClick={() => { setSearch(''); fetchBiographies(''); }}
                className="mt-2 text-sm text-blue-600 hover:underline cursor-pointer"
              >
                Reset Pencarian
              </button>
            )}
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {biographies.map((bio) => (
              <article 
                key={bio.id} 
                className="bg-white p-6 rounded-2xl border border-gray-200 hover:border-blue-300 transition-all shadow-sm hover:shadow-md flex flex-col justify-between"
              >
                <div>
                  <span className="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full uppercase tracking-wider">
                    {bio.author_name}
                  </span>
                  <h3 className="mt-3 text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                    <Link href={`/${bio.slug}`}>{bio.title}</Link>
                  </h3>
                  <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                    {bio.summary || 'Tidak ada ringkasan tersedia.'}
                  </p>
                </div>
                
                <div className="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                  <span>Dibuat: {new Date(bio.created_at).toLocaleDateString('id-ID')}</span>
                  <Link 
                    href={`/${bio.slug}`}
                    className="text-blue-600 hover:text-blue-800 font-semibold flex items-center"
                  >
                    Baca Selengkapnya <span className="ml-1">→</span>
                  </Link>
                </div>
              </article>
            ))}
          </div>
        )}
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
          <p>© 2026 Buku Kehidupan (Biography Wiki) - Sub-domain invertreview.com.</p>
        </div>
      </footer>
    </div>
  );
}
