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
            'x_coordinate' => 'required|string|size:1|lte:10',
            'y_coordinate' => 'required|numeric|gte:1|lte:10',
        ];
    }
}
