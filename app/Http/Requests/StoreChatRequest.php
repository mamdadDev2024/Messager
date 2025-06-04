<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
<<<<<<< Updated upstream
            'title' => 'required|string|max:100',
            'decription' => 'required|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
=======
            'title' => 'required|'
>>>>>>> Stashed changes
        ];
    }
}
