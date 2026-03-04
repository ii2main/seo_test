<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationUpdateRequest extends FormRequest
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
        $location = $this->route('location');

        return [
            'location_code' => [
                'required',
                'integer',
                Rule::unique('locations', 'location_code')->ignore($location?->id),
            ],
            'location_name' => ['required', 'string', 'max:255'],
            'location_code_parent' => ['nullable', 'integer'],
            'country_iso_code' => ['required', 'string', 'size:2'],
            'location_type' => ['nullable', 'string', 'max:255'],
        ];
    }
}
