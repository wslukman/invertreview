<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Siapa pun bisa register gereja (guest registration)
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
            'name' => 'required|string|max:255|unique:churches,name',
            'address' => 'required|string|min:10|max:500',
            'pastor_name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => ['required', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/', 'unique:churches,phone'],
            'email' => 'required|email:rfc,dns|unique:churches,email',
            'description' => 'required|string|min:20|max:1000',
            'founded_year' => 'required|integer|min:1900|max:' . date('Y'),
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama gereja harus diisi.',
            'name.unique' => 'Nama gereja sudah terdaftar.',
            'pastor_name.required' => 'Nama pendeta/pastor harus diisi.',
            'address.required' => 'Alamat lengkap harus diisi.',
            'address.min' => 'Alamat terlalu pendek (minimal 10 karakter).',
            'latitude.required' => 'Latitude harus diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka desimal.',
            'longitude.required' => 'Longitude harus diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka desimal.',
            'phone.required' => 'Nomor telepon harus diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid (gunakan format: +62xxxx atau 0xxxx).',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'description.required' => 'Deskripsi gereja harus diisi.',
            'description.min' => 'Deskripsi terlalu pendek (minimal 20 karakter).',
            'founded_year.required' => 'Tahun berdiri harus diisi.',
            'founded_year.integer' => 'Tahun berdiri harus berupa angka (YYYY).',
            'logo.image' => 'Logo harus berupa file gambar.',
            'logo.max' => 'Ukuran logo terlalu besar (maksimal 2 MB).',
            'cover_image.image' => 'Cover image harus berupa file gambar.',
            'cover_image.max' => 'Ukuran cover image terlalu besar (maksimal 3 MB).',
        ];
    }
}
