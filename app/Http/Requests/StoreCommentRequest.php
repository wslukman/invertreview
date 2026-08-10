<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Siapa pun bisa comment (guest atau auth)
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
            'name' => 'required_if:user_not_auth,true|nullable|string|max:255',
            'email' => 'required_if:user_not_auth,true|nullable|email:rfc,dns|max:255',
            'content' => 'required|string|min:5|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required_if' => 'Nama harus diisi jika Anda tidak login.',
            'name.max' => 'Nama terlalu panjang (maksimal 255 karakter).',
            'email.required_if' => 'Email harus diisi jika Anda tidak login.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'content.required' => 'Komentar tidak boleh kosong.',
            'content.min' => 'Komentar terlalu pendek (minimal 5 karakter).',
            'content.max' => 'Komentar terlalu panjang (maksimal 2000 karakter).',
        ];
    }
}
