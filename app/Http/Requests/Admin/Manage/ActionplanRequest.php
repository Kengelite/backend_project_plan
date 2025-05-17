<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ActionplanRequest extends FormRequest
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
            'action_plan_number' => 'required|string|max:255',
            'name_ap' => 'required|string|max:255',
            'id_strategic' => 'required|string|max:255',
            'id_year' => 'required|integer|digits_between:1,11',
            'budget' => 'required|numeric|between:0,99999999999.99',
        ];
    }

    public function messages()
    {
        return [
            'action_plan_number.required' => 'กรุณากรอกหมายเลขกลยุทธ์',
            'action_plan_number.string' => 'หมายเลขกลยุทธ์ต้องเป็นข้อความ',
            'action_plan_number.max' => 'หมายเลขกลยุทธ์ต้องไม่เกิน 255 ตัวอักษร',

            'name_ap.required' => 'กรุณากรอกชื่อกลยุทธ์',
            'name_ap.string' => 'ชื่อกลยุทธ์ต้องเป็นข้อความ',
            'name_ap.max' => 'ชื่อกลยุทธ์ต้องไม่เกิน 255 ตัวอักษร',

            'id_strategic.required' => 'กรุณาเลือกยุทธศาสตร์',
            'id_strategic.string' => 'รหัสยุทธศาสตร์ต้องเป็นข้อความ',
            'id_strategic.max' => 'รหัสยุทธศาสตร์ต้องไม่เกิน 255 ตัวอักษร',

            'id_year.required' => 'กรุณาเลือกปีงบประมาณ',
            'id_year.integer' => 'ปีงบประมาณต้องเป็นตัวเลข',
            'id_year.digits_between' => 'ปีงบประมาณต้องไม่เกิน 11 หลัก',

            'budget.required' => 'กรุณากรอกเงินงบประมาณ',
            'budget.numeric' => 'เงินงบประมาณต้องเป็นตัวเลข',
            'budget.between' => 'เงินงบประมาณต้องไม่เกิน 11 หลัก และมีทศนิยมไม่เกิน 2 ตำแหน่ง',
        ];
    }
}
