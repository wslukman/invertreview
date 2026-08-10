'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { marked } from 'marked';

export default function EditorPage() {
  const router = useRouter();
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [hasBiography, setHasBiography] = useState(false);
  const [isEditing, setIsEditing] = useState(false); // Mode edit biografi eksis

  // Form Fields
  const [title, setTitle] = useState('');
  const [slug, setSlug] = useState('');
  const [summary, setSummary] = useState('');
  const [contentMarkdown, setContentMarkdown] = useState('');
  const [isPublished, setIsPublished] = useState(true);
  const [lifeEvents, setLifeEvents] = useState([]);

  // UI state
  const [activeTab, setActiveTab] = useState('write'); // 'write' or 'preview'
  const [error, setError] = useState('');
  const [message, setMessage] = useState('');
  const [saving, setSaving] = useState(false);
  const [previewHtml, setPreviewHtml] = useState('');

  useEffect(() => {
    const initEditor = async () => {
      try {
        // 1. Verifikasi Autentikasi
        const meRes = await fetch('/api/auth/me');
        if (!meRes.ok) {
          router.push('/auth');
          return;
        }

        const meData = await meRes.json();
        setUser(meData.user);

        // 2. Cek apakah user sudah punya biografi
        if (meData.user.hasBiography) {
          setHasBiography(true);
          setIsEditing(true);
          
          // Ambil data biografi lengkap
          const bioRes = await fetch(`/api/biographies/${meData.user.biography.slug}`);
          if (bioRes.ok) {
            const bioData = await bioRes.json();
            setTitle(bioData.biography.title);
            setSlug(bioData.biography.slug);
            setSummary(bioData.biography.summary || '');
            setContentMarkdown(bioData.biography.content_markdown || '');
            setIsPublished(bioData.biography.is_published);
            setLifeEvents(bioData.lifeEvents || []);
          }
        }
      } catch (err) {
        setError('Gagal menghubungkan ke server.');
      } finally {
        setLoading(false);
      }
    };

    initEditor();
  }, [router]);

  // Generate slug otomatis dari title jika baru (bukan saat mengedit slug yang sudah ada)
  useEffect(() => {
    if (!isEditing && title) {
      const generatedSlug = title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // hapus non-alphanumeric
        .replace(/\s+/g, '-') // ganti spasi dengan strip
        .replace(/-+/g, '-') // bersihkan strip ganda
        .trim();
      setSlug(generatedSlug);
    }
  }, [title, isEditing]);

  // Update preview html ketika berpindah tab ke preview
  useEffect(() => {
    if (activeTab === 'preview') {
      const parsed = marked.parse(contentMarkdown || '*Belum ada konten ditulis.*');
      setPreviewHtml(parsed);
    }
  }, [activeTab, contentMarkdown]);

  // Menangani Milestone (Life Events)
  const addMilestone = () => {
    const newEvent = {
      event_year: new Date().getFullYear(),
      event_title: '',
      description: '',
      order_index: lifeEvents.length,
    };
    setLifeEvents([...lifeEvents, newEvent]);
  };

  const removeMilestone = (index) => {
    const updated = lifeEvents.filter((_, i) => i !== index);
    // Tata ulang order_index
    const reordered = updated.map((ev, idx) => ({ ...ev, order_index: idx }));
    setLifeEvents(reordered);
  };

  const updateMilestone = (index, field, value) => {
    const updated = [...lifeEvents];
    updated[index][field] = value;
    setLifeEvents(updated);
  };

  // Simpan/Kirim Data ke API
  const handleSave = async (e) => {
    e.preventDefault();
    setError('');
    setMessage('');
    setSaving(true);

    const body = {
      title,
      slug: slug.trim(),
      summary,
      contentMarkdown,
      isPublished,
      lifeEvents,
    };

    const endpoint = isEditing ? `/api/biographies/${slug}` : '/api/biographies';
    const method = isEditing ? 'PUT' : 'POST';

    try {
      const res = await fetch(endpoint, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
      });

      const data = await res.json();

      if (!res.ok) {
        throw new Error(data.error || 'Gagal menyimpan biografi.');
      }

      setMessage(data.message || 'Biografi berhasil disimpan!');
      setHasBiography(true);
      setIsEditing(true);
      // Sinkronkan data slug baru jika ada perubahan rute
      if (data.biography?.slug) {
        setSlug(data.biography.slug);
      }
    } catch (err) {
      setError(err.message);
    } finally {
      setSaving(false);
    }
  };

  // Logout
  const handleLogout = async () => {
    try {
      const res = await fetch('/api/auth/me', { method: 'POST' });
      if (res.ok) {
        localStorage.removeItem('auth_token');
        router.push('/auth');
      }
    } catch (e) {
      setError('Gagal logout.');
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="flex flex-col items-center space-y-4">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-600" />
          <p className="text-gray-500 font-medium">Menyiapkan editor...</p>
        </div>
      </div>
    );
  }

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
            <span className="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded font-mono">Editor</span>
          </div>
          <div className="flex items-center space-x-4">
            {isEditing && (
              <a
                href={`/${slug}`}
                target="_blank"
                rel="noreferrer"
                className="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-xl font-semibold transition-all border border-gray-200"
              >
                👁️ Lihat Biografi Anda
              </a>
            )}
            <button
              onClick={handleLogout}
              className="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-xl font-semibold transition-all border border-red-100 cursor-pointer"
            >
              Keluar
            </button>
          </div>
        </div>
      </header>

      {/* Main Container */}
      <main className="flex-grow max-w-6xl mx-auto px-4 py-8 w-full">
        {/* Status / Alert */}
        {error && (
          <div className="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
            <p className="text-sm text-red-700 font-medium">{error}</p>
          </div>
        )}
        {message && (
          <div className="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
            <p className="text-sm text-green-700 font-medium">{message}</p>
          </div>
        )}

        <form onSubmit={handleSave} className="space-y-8">
          {/* Section 1: Pengaturan Profil Utama */}
          <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            <h2 className="text-xl font-bold text-gray-900 border-b border-gray-100 pb-2 flex items-center">
              <span className="mr-2">👤</span> Profil Biografi Utama
            </h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Judul Biografi */}
              <div className="space-y-1">
                <label className="block text-sm font-semibold text-gray-700">
                  Judul Biografi (Nama Tokoh)
                </label>
                <input
                  type="text"
                  required
                  value={title}
                  onChange={(e) => setTitle(e.target.value)}
                  placeholder="Contoh: Ir. Soekarno"
                  className="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900"
                />
              </div>

              {/* Slug URL */}
              <div className="space-y-1">
                <label className="block text-sm font-semibold text-gray-700">
                  Custom Slug URL (Nama di Link)
                </label>
                <div className="flex rounded-xl shadow-xs">
                  <span className="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-gray-300 bg-gray-100 text-gray-500 text-xs sm:text-sm font-mono">
                    /{''}
                  </span>
                  <input
                    type="text"
                    required
                    disabled={isEditing} // Kunci slug jika sudah tersimpan demi integritas URL
                    value={slug}
                    onChange={(e) => setSlug(e.target.value.toLowerCase().replace(/\s+/g, '-'))}
                    placeholder="nama-tokoh"
                    className="w-full px-4 py-2.5 border border-gray-300 rounded-r-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed font-mono"
                  />
                </div>
                {isEditing && (
                  <small className="text-gray-400 text-xs">Slug tidak dapat diubah setelah disimpan untuk menjaga stabilitas link.</small>
                )}
              </div>
            </div>

            {/* Ringkasan Singkat */}
            <div className="space-y-1">
              <label className="block text-sm font-semibold text-gray-700">
                Ringkasan Biografi (Satu Paragraf Pendek)
              </label>
              <textarea
                rows="2"
                value={summary}
                onChange={(e) => setSummary(e.target.value)}
                placeholder="Tulis ringkasan hidup tokoh dalam 1-2 kalimat..."
                className="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900"
              />
            </div>
          </div>

          {/* Section 2: Markdown Editor & Preview */}
          <div className="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div className="border-b border-gray-200 bg-gray-50 px-6 py-4 flex items-center justify-between">
              <h2 className="text-xl font-bold text-gray-900 flex items-center">
                <span className="mr-2">📝</span> Kisah Hidup Lengkap
              </h2>
              {/* Tab selector */}
              <div className="flex border border-gray-300 rounded-lg overflow-hidden text-xs">
                <button
                  type="button"
                  onClick={() => setActiveTab('write')}
                  className={`px-4 py-2 font-semibold transition-colors cursor-pointer ${
                    activeTab === 'write' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                  }`}
                >
                  Tulis (Markdown)
                </button>
                <button
                  type="button"
                  onClick={() => setActiveTab('preview')}
                  className={`px-4 py-2 font-semibold transition-colors cursor-pointer ${
                    activeTab === 'preview' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
                  }`}
                >
                  Preview HTML
                </button>
              </div>
            </div>

            <div className="p-6">
              {activeTab === 'write' ? (
                <div className="space-y-2">
                  <textarea
                    required
                    rows="15"
                    value={contentMarkdown}
                    onChange={(e) => setContentMarkdown(e.target.value)}
                    placeholder="Tuliskan kisah hidup lengkap di sini menggunakan sintaks Markdown...&#13;Contoh:&#13;# Masa Kecil&#13;Beliau lahir di...&#13;&#13;## Pencapaian Karir&#13;- Mendirikan organisasi...&#13;- Menulis buku..."
                    className="w-full p-4 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 font-mono text-sm"
                  />
                  <small className="text-gray-400 block text-xs">
                    Tips: Gunakan `# Judul` untuk Heading 1, `## Sub-judul` untuk Heading 2, `**tebal**`, `*miring*`, `- list item`.
                  </small>
                </div>
              ) : (
                <div 
                  className="prose max-w-none p-4 border border-gray-200 rounded-xl min-h-[300px] bg-gray-50 text-gray-800 space-y-4"
                  dangerouslySetInnerHTML={{ __html: previewHtml }}
                />
              )}
            </div>
          </div>

          {/* Section 3: Garis Waktu Hidup (Milestones) */}
          <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            <div className="flex items-center justify-between border-b border-gray-100 pb-2">
              <h2 className="text-xl font-bold text-gray-900 flex items-center">
                <span className="mr-2">⏳</span> Garis Waktu Kehidupan (Milestones)
              </h2>
              <button
                type="button"
                onClick={addMilestone}
                className="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all border border-blue-100 cursor-pointer flex items-center gap-1"
              >
                ➕ Tambah Peristiwa
              </button>
            </div>

            {lifeEvents.length === 0 ? (
              <div className="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <p className="text-gray-500 text-sm">Belum ada peristiwa milestone. Klik tombol di atas untuk menambahkan peristiwa penting kehidupan (seperti tahun kelahiran, kelulusan, pernikahan, dll).</p>
              </div>
            ) : (
              <div className="space-y-4">
                {lifeEvents.map((event, index) => (
                  <div 
                    key={index} 
                    className="p-5 bg-gray-50 rounded-xl border border-gray-200 flex flex-col gap-4 shadow-2xs relative"
                  >
                    {/* Hapus Button */}
                    <button
                      type="button"
                      onClick={() => removeMilestone(index)}
                      className="absolute top-4 right-4 text-red-500 hover:text-red-700 text-sm font-semibold cursor-pointer"
                    >
                      Hapus
                    </button>

                    <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 pr-12">
                      {/* Tahun */}
                      <div className="sm:col-span-1 space-y-1">
                        <label className="block text-xs font-semibold text-gray-600">Tahun</label>
                        <input
                          type="number"
                          required
                          value={event.event_year}
                          onChange={(e) => updateMilestone(index, 'event_year', e.target.value)}
                          placeholder="2026"
                          className="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white"
                        />
                      </div>
                      
                      {/* Judul Peristiwa */}
                      <div className="sm:col-span-3 space-y-1">
                        <label className="block text-xs font-semibold text-gray-600">Nama Peristiwa</label>
                        <input
                          type="text"
                          required
                          value={event.event_title}
                          onChange={(e) => updateMilestone(index, 'event_title', e.target.value)}
                          placeholder="Lulus Kuliah / Menikah / Menjabat Presiden"
                          className="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white"
                        />
                      </div>
                    </div>

                    {/* Deskripsi */}
                    <div className="space-y-1">
                      <label className="block text-xs font-semibold text-gray-600">Deskripsi Detail (Opsional)</label>
                      <textarea
                        rows="2"
                        value={event.description}
                        onChange={(e) => updateMilestone(index, 'description', e.target.value)}
                        placeholder="Detail tambahan cerita peristiwa..."
                        className="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm bg-white"
                      />
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Pengaturan Publikasi & Tombol Simpan */}
          <div className="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div className="flex items-center space-x-3">
              <input
                id="is-published"
                type="checkbox"
                checked={isPublished}
                onChange={(e) => setIsPublished(e.target.checked)}
                className="h-5 w-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer"
              />
              <label htmlFor="is-published" className="text-sm font-semibold text-gray-700 cursor-pointer select-none">
                Publikasikan biografi ini secara umum (Dapat dibaca oleh semua orang)
              </label>
            </div>

            <button
              type="submit"
              disabled={saving}
              className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md hover:shadow-lg transition-all disabled:opacity-50 cursor-pointer text-center shrink-0"
            >
              {saving ? 'Menyimpan...' : 'Simpan Biografi'}
            </button>
          </div>
        </form>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t border-gray-200 py-8 mt-12">
        <div className="max-w-6xl mx-auto px-4 text-center text-sm text-gray-500">
          <p>© 2026 Buku Kehidupan (Biography Wiki) - Sub-domain invertreview.com.</p>
        </div>
      </footer>
    </div>
  );
}
