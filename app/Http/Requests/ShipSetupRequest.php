<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipSetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'shipPositions' => 'required|array',
            'shipPositions.*.length' => 'required|in:2,3,4',
            'shipPositions.*.positions' => 'required|array',
            'shipPositions.*.positions.*.from.row' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.from.col' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.to.row' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.to.col' => 'required|integer|gte:1|lte:10',
        ];
    }
}

