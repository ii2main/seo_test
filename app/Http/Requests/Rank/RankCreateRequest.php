<?php

    namespace App\Http\Requests\Rank;

    use Illuminate\Foundation\Http\FormRequest;

    class RankCreateRequest extends FormRequest
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
            return [
                'domain_id' => ['required', 'integer', 'exists:domains,id'],
                'location_id' => ['required', 'integer', 'exists:locations,id'],
                'language_id' => ['required', 'integer', 'exists:languages,id'],
                'keyword' => ['required', 'string', 'max:255'],
            ];
        }
    }
