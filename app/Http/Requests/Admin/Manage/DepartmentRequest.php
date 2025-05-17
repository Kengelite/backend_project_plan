<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'departments_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'departments_name.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'departments_name.string' => 'ชื่อหน่วยงานต้องเป็นข้อความ',
            'departments_name.max' => 'ชื่อหน่วยงานต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
