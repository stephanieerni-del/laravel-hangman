<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGameRequest extends FormRequest
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
        $game = $this->route()->game;
        $stage = $game->play($this->user());

        return [
            'guess' => [
                'required',
                'string',
                'size:1',
                'regex:/^[a-zA-Z]$/',
                Rule::notIn($stage->getGuesses()),
            ],
        ];
    }
}
