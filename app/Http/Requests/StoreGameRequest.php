<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'unique:games',
            ],
            'difficulty' => [
                'required',
                'in:easy,medium,hard,extreme',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'The :attribute is required.',
            'unique' => 'The :attribute is already taken',
            'in' => 'The selected :attribute is invalid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'game name',
            'difficulty' => 'difficulty',
        ];
    }
}
