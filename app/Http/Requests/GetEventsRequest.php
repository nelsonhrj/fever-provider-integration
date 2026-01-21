<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetEventsRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'starts_at' => [
                'required',
                'date',
                'date_format:Y-m-d\TH:i:s\Z',
                'before_or_equal:ends_at',
            ],
            'ends_at' => [
                'required',
                'date',
                'date_format:Y-m-d\TH:i:s\Z',
                'after_or_equal:starts_at',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'starts_at.required'       => 'The starts_at parameter is required.',
            'ends_at.required'         => 'The ends_at parameter is required.',
            'starts_at.date'           => 'The starts_at must be a valid date.',
            'ends_at.date'             => 'The ends_at must be a valid date.',
            'starts_at.date_format'    => 'The starts_at must be in ISO 8601 UTC format (e.g., 2025-01-01T00:00:00Z)',
            'ends_at.date_format'      => 'The ends_at must be in ISO 8601 UTC format (e.g., 2025-01-01T23:59:59Z)',
            'starts_at.before_or_equal' => 'The starts_at must be a date before or equal to ends_at.',
            'ends_at.after_or_equal'   => 'The ends_at must be a date after or equal to starts_at.',
        ];
    }
}
