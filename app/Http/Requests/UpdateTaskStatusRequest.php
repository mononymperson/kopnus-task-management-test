<?php

namespace App\Http\Requests;

use App\Models\Task;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([Task::STATUS_PENDING, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETED]),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $task = $this->route('task');
                    if ($task instanceof Task && ! $task->canTransitionTo($value)) {
                        $fail('Perubahan status tidak valid. Status hanya boleh maju satu tahap: pending ke in_progress, lalu in_progress ke completed.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status tidak dikenali.',
        ];
    }
}
