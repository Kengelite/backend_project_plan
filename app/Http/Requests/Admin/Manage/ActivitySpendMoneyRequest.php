<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ActivitySpendMoneyRequest extends FormRequest
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
            'id_activity' => 'required|string|max:255',
            'activity_spendmoney_name' => 'required|string|max:255',
            'id_unit' => 'required|int',
        ];
    }

    public function messages(): array
    {
        return [
            // id_activity
            'id_activity.required' => 'กรุณาระบุรหัสกิจกรรม',
            'id_activity.string' => 'รหัสกิจกรรมต้องเป็นข้อความ',
            'id_activity.max' => 'รหัสกิจกรรมต้องไม่เกิน 255 ตัวอักษร',

            // activity_spendmoney_name
            'activity_spendmoney_name.required' => 'กรุณากรอกชื่อรายการค่าใช้จ่าย',
            'activity_spendmoney_name.string' => 'ชื่อรายการต้องเป็นข้อความ',
            'activity_spendmoney_name.max' => 'ชื่อรายการต้องไม่เกิน 255 ตัวอักษร',

            // id_unit
            'id_unit.required' => 'กรุณาเลือกหน่วยนับ',
            'id_unit.integer' => 'หน่วยนับต้องเป็นตัวเลขเท่านั้น',
        ];
    }
}
