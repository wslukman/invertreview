'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { useParams, useRouter } from 'next/navigation';
import { marked } from 'marked';

export default function ProfilePage() {
  const { slug } = useParams();
  const router = useRouter();
  const [data, setData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [currentUser, setCurrentUser] = useState(null);
  const [htmlContent, setHtmlContent] = useState('');

  useEffect(() => {
    const checkUserAndFetchBio = async () => {
      // 1. Ambil session user
      try {
        const userRes = await fetch('/api/auth/me');
        if (userRes.ok) {
          const userData = await userRes.json();
          setCurrentUser(userData.user);
        }
      } catch (e) {
        // Abaikan
      }

      // 2. Ambil data biografi
      try {
        const res = await fetch(`/api/biographies/${slug}`);
        const result = await res.json();

        if (!res.ok) {
          throw new Error(result.error || 'Gagal mengambil data.');
        }

        setData(result);
        
        // Parse markdown content to html
        if (result.biography?.content_markdown) {
          const parsedHtml = marked.parse(result.biography.content_markdown);
          setHtmlContent(parsedHtml);
        }
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    checkUserAndFetchBio();
  }, [slug]);

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="flex flex-col items-center space-y-4">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600" />
          <p className="text-gray-500 font-medium">Memuat biografi...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
        <div className="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-md border border-gray-200">
          <span className="text-5xl block mb-4">⚠️</span>
          <h2 className="text-2xl font-bold text-gray-900">Oops!</h2>
          <p className="mt-2 text-gray-600">{error}</p>
          <div className="mt-6 flex justify-center gap-4">
            <Link href="/" className="bg-gray-900 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-800 transition-all">
              Kembali ke Beranda
            </Link>
          </div>
        </div>
      </div>
    );
  }

  const { biography, lifeEvents } = data;
  const isOwner = currentUser && currentUser.id === biography.user_id;

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col font-sans">
      {/* Header */}
      <header className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <span className="text-2xl">📖</span>
            <Link href="/" className="text-xl font-bold text-gray-900 tracking-tight hover:text-blue-600 transition-colors">
              Buku Kehidupan
            </Link>
          </div>
          <div className="flex items-center space-x-4">
            {isOwner && (
              <Link
                href="/editor"
                className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all shadow-sm"
              >
                ✏️ Edit Biografi Anda
              </Link>
            )}
            <Link
              href="/"
              className="border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition-all"
            >
              Kembali
            </Link>
          </div>
        </div>
      </header>

      {/* Profile Cover & Summary */}
      <section className="bg-white border-b border-gray-200 py-12">
        <div className="max-w-4xl mx-auto px-4">
          <div className="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div className="w-24 h-24 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-4xl font-bold border-4 border-white shadow-md shrink-0">
              {biography.author_name ? biography.author_name.charAt(0).toUpperCase() : 'U'}
            </div>
            <div className="flex-grow text-center sm:text-left space-y-2">
              <div className="flex flex-col sm:flex-row sm:items-center gap-2 justify-center sm:justify-start">
                <h1 className="text-3xl font-extrabold text-gray-900">{biography.title}</h1>
                {!biography.is_published && (
                  <span className="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded-full w-fit mx-auto sm:mx-0">
                    Draft (Privat)
                  </span>
                )}
              </div>
              <p className="text-sm font-medium text-blue-600">Oleh: {biography.author_name}</p>
              {biography.summary && (
                <p className="text-gray-600 text-base max-w-2xl leading-relaxed italic">
                  "{biography.summary}"
                </p>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Main Content (Markdown + Timeline) */}
      <main className="flex-grow max-w-4xl mx-auto px-4 py-12 w-full grid grid-cols-1 gap-12">
        {/* Cerita Biografi Utama */}
        <section className="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
          <h2 className="text-2xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-100">
            📝 Kisah Riwayat Hidup
          </h2>
          <div 
            className="prose max-w-none text-gray-800 leading-relaxed space-y-4"
            dangerouslySetInnerHTML={{ __html: htmlContent }}
          />
        </section>

        {/* Timeline / Garis Waktu Hidup */}
        <section className="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
          <h2 className="text-2xl font-bold text-gray-900 mb-8 pb-2 border-b border-gray-100">
            ⏳ Garis Waktu Kehidupan (Milestones)
          </h2>

          {lifeEvents.length === 0 ? (
            <p className="text-gray-500 italic text-center py-6">Belum ada peristiwa milestone yang ditambahkan.</p>
          ) : (
            <div className="relative border-l-2 border-blue-200 ml-4 md:ml-32 space-y-8">
              {lifeEvents.map((event, index) => (
                <div key={event.id || index} className="relative pl-6 md:pl-8">
                  {/* Circle Indicator on the line */}
                  <span className="absolute -left-[9px] top-1.5 bg-blue-600 border-4 border-white h-4.5 w-4.5 rounded-full shadow-sm" />

                  {/* Year Display (Left of the line on desktop, top of content on mobile) */}
                  <div className="md:absolute md:-left-32 md:w-24 md:text-right md:top-0 font-bold text-blue-600 text-lg mb-1 md:mb-0">
                    {event.event_year}
                  </div>

                  {/* Content Box */}
                  <div className="bg-gray-50 p-5 rounded-xl border border-gray-100 shadow-xs hover:border-blue-100 transition-colors">
                    <h3 className="text-lg font-bold text-gray-900">{event.event_title}</h3>
                    {event.description && (
                      <p className="mt-2 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{event.description}</p>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </section>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 py-8 mt-12">
        <div className="max-w-4xl mx-auto px-4 text-center text-sm text-gray-500">
          <p>© 2026 Buku Kehidupan (Biography Wiki) - Sub-domain invertreview.com.</p>
        </div>
      </footer>
    </div>
  );
}
