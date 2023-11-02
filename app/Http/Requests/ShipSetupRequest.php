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
            'shipPositions.*.positions.*.from_row' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.from_col' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.to_row' => 'required|integer|gte:1|lte:10',
            'shipPositions.*.positions.*.to_col' => 'required|integer|gte:1|lte:10',
        ];
    }

    public function messages()
    {
        return [
            'shipPositions.required' => 'Please provide the positions array',
            'shipPositions.required' => 'The ships positions have to be arranged into an array',
            'shipPositions.*.length.required' => 'Each ship in array has to have a length',
            'shipPositions.*.length.in' => 'The ship\'s length should be 2, 3 or 4',
            'shipPositions.*.positions.required' => 'Please provide an array of positions for each ship',
            'shipPositions.*.positions.array' => 'The ships positions have to be arranged into an array',
            'shipPositions.*.positions.*.from_row.required' => 'The from_row coordinate is required',
            'shipPositions.*.positions.*.from_row.integer' => 'The from_row coordinate has to be a number',
            'shipPositions.*.positions.*.from_row.gte' => 'The from_row coordinate has to be greater than 1 or equal',
            'shipPositions.*.positions.*.from_row.lte' => 'The from_row coordinate has to be smaller than 10 or equal',
            'shipPositions.*.positions.*.from_col.required' => 'The from_col coordinate is required',
            'shipPositions.*.positions.*.from_col.integer' => 'The from_col coordinate has to be a number',
            'shipPositions.*.positions.*.from_col.gte' => 'The from_col coordinate has to be greater than 1 or equal',
            'shipPositions.*.positions.*.from_col.lte' => 'The from_col coordinate has to be smaller than 10 or equal',
            'shipPositions.*.positions.*.to_row.required' => 'The to_row coordinate is required',
            'shipPositions.*.positions.*.to_row.integer' => 'The to_row coordinate has to be a number',
            'shipPositions.*.positions.*.to_row.gte' => 'The to_row coordinate has to be greater than 1 or equal',
            'shipPositions.*.positions.*.to_row.lte' => 'The to_row coordinate has to be smaller than 10 or equal',
            'shipPositions.*.positions.*.to_col.required' => 'The to_col coordinate is required',
            'shipPositions.*.positions.*.to_col.integer' => 'The to_col coordinate has to be a number',
            'shipPositions.*.positions.*.to_col.gte' => 'The to_col coordinate has to be greater than 1 or equal',
            'shipPositions.*.positions.*.to_col.lte' => 'The to_col coordinate has to be smaller than 10 or equal',
        ];
    }
}

