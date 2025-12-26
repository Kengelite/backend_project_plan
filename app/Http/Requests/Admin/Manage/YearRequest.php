<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class YearRequest extends FormRequest
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
            'year' => 'required|string|max:4',
        ];
    }

    public function messages()
    {
        return [
            'year.required' => 'กรุณากรอกชื่อปี',
            'year.string' => 'ชื่อประเภทต้องเป็นข้อความ',
            'year.max' => 'ชื่อประเภทต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
