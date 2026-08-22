<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10|max:5000',
            // Tambahkan tipe lain jika diperlukan dalam ekosistem qq/United
            'type' => 'required|string|in:ibadah,kegiatan_sosial,pelatihan,pertemuan', 
            'activity_date' => 'required|date', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // PENTING: Tambahkan church_id di sini agar bisa divalidasi jika dikirim oleh Super Admin
            'church_id' => 'nullable|exists:churches,id',
            'is_published' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul aktivitas harus diisi.',
            'title.max' => 'Judul terlalu panjang (maksimal 255 karakter).',
            'content.required' => 'Konten aktivitas harus diisi.',
            'content.min' => 'Konten terlalu pendek (minimal 10 karakter).',
            'type.required' => 'Tipe aktivitas harus dipilih.',
            'type.in' => 'Tipe aktivitas yang dipilih tidak valid.',
            'activity_date.required' => 'Tanggal aktivitas harus diisi.',
            'activity_date.date' => 'Format tanggal tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'church_id.exists' => 'Gereja yang dipilih tidak terdaftar di sistem.',
        ];
    }
}