<?php
namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title'            => 'required|string|max:255',
            'description'      => 'required|string',
            'project_id'       => 'required|exists:projects,id',
            'assigned_user_id' => 'required|exists:users,id',
            'status'           => 'sometimes|in:To Do,In Progress,Done',
            'priority'         => 'sometimes|in:Low,Medium,High,Urgent',
            'due_date'         => 'required|date|after:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required'            => 'عنوان المهمة مطلوب',
            'title.max'                 => 'عنوان المهمة يجب أن لا يتجاوز 255 حرف',
            'description.required'      => 'وصف المهمة مطلوب',
            'project_id.required'       => 'المشروع مطلوب',
            'project_id.exists'         => 'المشروع غير موجود',
            'assigned_user_id.required' => 'المستخدم المكلف مطلوب',
            'assigned_user_id.exists'   => 'المستخدم المكلف غير موجود',
            'status.in'                 => 'حالة المهمة غير صحيحة',
            'priority.in'               => 'أولوية المهمة غير صحيحة',
            'due_date.required'         => 'تاريخ الاستحقاق مطلوب',
            'due_date.date'             => 'تاريخ الاستحقاق يجب أن يكون تاريخ صحيح',
            'due_date.after'            => 'تاريخ الاستحقاق يجب أن يكون بعد اليوم',
        ];
    }
}
