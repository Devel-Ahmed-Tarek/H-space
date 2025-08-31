<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:Admin,Project Manager,Developer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required'        => 'الاسم مطلوب',
            'name.max'            => 'الاسم يجب أن لا يتجاوز 255 حرف',
            'email.required'      => 'البريد الإلكتروني مطلوب',
            'email.email'         => 'البريد الإلكتروني يجب أن يكون صحيح',
            'email.max'           => 'البريد الإلكتروني يجب أن لا يتجاوز 255 حرف',
            'email.unique'        => 'البريد الإلكتروني مستخدم بالفعل',
            'password.required'   => 'كلمة المرور مطلوبة',
            'password.min'        => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed'  => 'تأكيد كلمة المرور غير متطابق',
            'role.required'       => 'الدور مطلوب',
            'role.in'             => 'الدور غير صحيح',
        ];
    }
}
