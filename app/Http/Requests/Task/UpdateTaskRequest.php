<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isProjectManager();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'            => 'sometimes|string|max:255',
            'description'      => 'sometimes|string',
            'project_id'       => 'sometimes|exists:projects,id',
            'assigned_user_id' => 'sometimes|exists:users,id',
            'status'           => 'sometimes|in:To Do,In Progress,Done',
            'priority'         => 'sometimes|in:Low,Medium,High,Urgent',
            'due_date'         => 'sometimes|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.max'                 => 'عنوان المهمة يجب أن لا يتجاوز 255 حرف',
            'project_id.exists'         => 'المشروع غير موجود',
            'assigned_user_id.exists'   => 'المستخدم المكلف غير موجود',
            'status.in'                 => 'حالة المهمة غير صحيحة',
            'priority.in'               => 'أولوية المهمة غير صحيحة',
            'due_date.date'             => 'تاريخ الاستحقاق يجب أن يكون تاريخ صحيح',
            'due_date.after'            => 'تاريخ الاستحقاق يجب أن يكون بعد اليوم',
        ];
    }
}
