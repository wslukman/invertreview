<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgramRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Siapa pun bisa register (guest atau auth)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $isGuest = !auth()->check();

        return [
            'guest_name' => $isGuest ? 'required|string|max:255' : 'nullable|string|max:255',
            'guest_email' => $isGuest ? 'required|email:rfc,dns|max:255' : 'nullable|email:rfc,dns|max:255',
            'guest_phone' => $isGuest 
                ? ['required', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/'] 
                : ['nullable', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'guest_name.required' => 'Nama harus diisi.',
            'guest_name.max' => 'Nama terlalu panjang (maksimal 255 karakter).',
            'guest_email.required' => 'Email harus diisi.',
            'guest_email.email' => 'Format email tidak valid.',
            'guest_email.max' => 'Email terlalu panjang.',
            'guest_phone.required' => 'Nomor telepon harus diisi.',
            'guest_phone.regex' => 'Format nomor telepon tidak valid (gunakan format: +62xxxx atau 0xxxx).',
        ];
    }
}
