<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttackCoordinatesRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'x_coordinate' => 'required|string|size:1|in:A,B,C,D,E,F,G,H,I,J',
            'y_coordinate' => 'required|numeric|gte:1|lte:10',
        ];
    }

    public function messages(): array
    {
        return [
            'x_coordinate.required' => 'Please provide the X coordinate',
            'x_coordinate.string' => 'The X coordinate has to be a letter from A-J',
            'x_coordinate.size' => 'Please provide only a single letter for the X coordinate',
            'x_coordinate.in' => 'The X coordinate has to be a letter from A-J',
            'y_coordinate' => 'Please provide the Y coordinate',
            'y_coordinate.numeric' => 'The Y coordinate has to be a number',
            'y_coordinate.gte' => 'The Y coordinate cannot be greater than 10',
            'y_coordinate.lte' => 'The Y coordinate cannot be smaller than 1',
        ];
    }
}
