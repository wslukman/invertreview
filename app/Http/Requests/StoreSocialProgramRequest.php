<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in SocialProgramController
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20|max:3000',
            'type' => 'required|in:pelatihan,pemberian_makanan,kesehatan,pendidikan,lainnya',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'capacity' => 'required|integer|min:1|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => ['required', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul program harus diisi.',
            'title.max' => 'Judul terlalu panjang (maksimal 255 karakter).',
            'description.required' => 'Deskripsi program harus diisi.',
            'description.min' => 'Deskripsi terlalu pendek (minimal 20 karakter).',
            'description.max' => 'Deskripsi terlalu panjang (maksimal 3000 karakter).',
            'type.required' => 'Tipe program harus dipilih.',
            'type.in' => 'Tipe program tidak valid.',
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.date' => 'Format tanggal tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai harus hari ini atau di masa depan.',
            'end_date.date' => 'Format tanggal tidak valid.',
            'end_date.after_or_equal' => 'Tanggal berakhir harus setelah atau sama dengan tanggal mulai.',
            'capacity.required' => 'Kapasitas peserta harus diisi.',
            'capacity.integer' => 'Kapasitas harus berupa angka.',
            'capacity.min' => 'Kapasitas minimal 1 peserta.',
            'capacity.max' => 'Kapasitas maksimal 10000 peserta.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar terlalu besar (maksimal 3 MB).',
            'contact_person.required' => 'Nama kontak harus diisi.',
            'contact_phone.required' => 'Telepon kontak harus diisi.',
            'contact_phone.regex' => 'Format telepon tidak valid (gunakan format: +62xxxx atau 0xxxx).',
        ];
    }
}
