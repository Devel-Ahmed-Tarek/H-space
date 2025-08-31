<?php
namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
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
            'name'               => 'sometimes|string|max:255',
            'description'        => 'sometimes|string',
            'project_manager_id' => 'sometimes|exists:users,id',
            'start_date'         => 'sometimes|date',
            'end_date'           => 'sometimes|date|after:start_date',
            'status'             => 'sometimes|in:Planning,In Progress,Completed,On Hold,Cancelled',
            'is_approved'        => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max'                  => 'اسم المشروع يجب أن لا يتجاوز 255 حرف',
            'project_manager_id.exists' => 'مدير المشروع غير موجود',
            'start_date.date'           => 'تاريخ البداية يجب أن يكون تاريخ صحيح',
            'end_date.date'             => 'تاريخ النهاية يجب أن يكون تاريخ صحيح',
            'end_date.after'            => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'status.in'                 => 'حالة المشروع غير صحيحة',
            'is_approved.boolean'       => 'حقل الموافقة يجب أن يكون صحيح أو خطأ',
        ];
    }
}
