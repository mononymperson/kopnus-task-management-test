<?php

namespace App\Http\Requests;

use App\Models\ActivityLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['nullable', 'string', Rule::in(array_keys(ActivityLog::ACTIONS))],
            'from' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:to'],
            'to' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.in' => 'Jenis aksi tidak dikenali.',
            'from.date_format' => 'Format tanggal awal harus YYYY-MM-DD.',
            'from.before_or_equal' => 'Tanggal awal tidak boleh setelah tanggal akhir.',
            'to.date_format' => 'Format tanggal akhir harus YYYY-MM-DD.',
        ];
    }
}
