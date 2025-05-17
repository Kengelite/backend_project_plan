<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class StrategicRequest extends FormRequest
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
            'strategic_name' => 'required|string|max:255',
            'strategic_number' => 'required|string|max:255',
            'id_year' => 'required|integer|digits_between:1,11',
            'budget' => 'required|numeric|between:0,99999999999.99',
        ];
    }
    public function messages()
    {
        return [
            'strategic_name.required' => 'กรุณากรอกชื่อยุทธศาสตร์',
            'strategic_name.string' => 'ชื่อยุทธศาสตร์ต้องเป็นข้อความ',
            'strategic_name.max' => 'ชื่อยุทธศาสตร์ต้องไม่เกิน 255 ตัวอักษร',

            'strategic_number.required' => 'กรุณากรอกหมายเลขยุทธศาสตร์',
            'strategic_number.string' => 'หมายเลขยุทธศาสตร์ต้องเป็นข้อความ',
            'strategic_number.max' => 'หมายเลขยุทธศาสตร์ต้องไม่เกิน 255 ตัวอักษร',

            'id_year.required' => 'กรุณาเลือกปีงบประมาณ',
            'id_year.int' => 'ปีงบประมาณต้องเป็นตัวเลข',
            'id_year.digits_between' => 'ปีงบประมาณต้องไม่เกิน 11 หลัก',

            'budget.required' => 'กรุณากรอกเงินงบประมาณ',
            'budget.numeric' => 'เงินงบประมาณต้องเป็นตัวเลข',
            'budget.between' => 'เงินงบประมาณต้องไม่เกิน 11 หลัก',
        ];
    }
}
