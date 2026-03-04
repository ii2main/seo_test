<?php

namespace App\Http\Requests\Language;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LanguageUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $language = $this->route('language');

        return [
            'language_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('languages', 'language_code')->ignore($language?->id),
            ],
           'language_name' => ['required', 'string', 'max:255'],
        ];
    }
}
