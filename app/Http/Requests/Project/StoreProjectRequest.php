<?php
namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name'               => 'required|string|max:255',
            'description'        => 'required|string',
            'project_manager_id' => 'required|exists:users,id',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'               => 'اسم المشروع مطلوب',
            'name.max'                    => 'اسم المشروع يجب أن لا يتجاوز 255 حرف',
            'description.required'        => 'وصف المشروع مطلوب',
            'project_manager_id.required' => 'مدير المشروع مطلوب',
            'project_manager_id.exists'   => 'مدير المشروع غير موجود',
            'start_date.required'         => 'تاريخ البداية مطلوب',
            'start_date.date'             => 'تاريخ البداية يجب أن يكون تاريخ صحيح',
            'end_date.required'           => 'تاريخ النهاية مطلوب',
            'end_date.date'               => 'تاريخ النهاية يجب أن يكون تاريخ صحيح',
            'end_date.after'              => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
        ];
    }
}
