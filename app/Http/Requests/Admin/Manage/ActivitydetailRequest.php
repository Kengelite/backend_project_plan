<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ActivitydetailRequest extends FormRequest
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
            'detail' => 'required|string|max:255',
            'price' => 'required|numeric|between:0,99999999999.99',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'station' => 'required|string|max:255',
            'report_data' => 'required|string',
            'id_employee' => 'required|string|max:255',
            'id_activity' => 'required|string|max:255',
            'activitiydetailspendmoney' => 'required|array',
        ];
    }

    public function messages()
    {
        return [
            'detail.required' => 'กรุณากรอกรายละเอียด',
            'detail.string' => 'รายละเอียดต้องเป็นข้อความ',
            'detail.max' => 'รายละเอียดต้องไม่เกิน 255 ตัวอักษร',

            'price.required' => 'กรุณากรอกราคา',
            'price.numeric' => 'ราคาต้องเป็นตัวเลข',
            'price.between' => 'ราคาต้องอยู่ระหว่าง 0 ถึง 999,999.99',

            'start_date.required' => 'กรุณาเลือกวันที่เริ่มต้น',
            'start_date.date' => 'วันที่เริ่มต้นต้องเป็นวันที่ที่ถูกต้อง',

            'end_date.required' => 'กรุณาเลือกวันที่สิ้นสุด',
            'end_date.date' => 'วันที่สิ้นสุดต้องเป็นวันที่ที่ถูกต้อง',
            'end_date.after_or_equal' => 'วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มต้น',

            'report_data.required' => 'กรุณาเลือกวันที่รายงาน',
            'report_data.string' => 'วันที่รายงานต้องเป็นวันที่ที่ถูกต้อง',

            'station.required' => 'กรุณากรอกสถานที่',
            'station.string' => 'สถานที่ต้องเป็นข้อความ',
            'station.max' => 'สถานที่ต้องไม่เกิน 255 ตัวอักษร',



            'id_employee.required' => 'กรุณาเลือกพนักงาน',
            'id_employee.string' => 'รหัสพนักงานต้องเป็นข้อความ',
            'id_employee.max' => 'รหัสพนักงานต้องไม่เกิน 255 ตัวอักษร',

            'id_activity.required' => 'กรุณาเลือกกิจกรรม',
            'id_activity.string' => 'รหัสกิจกรรมต้องเป็นข้อความ',
            'id_activity.max' => 'รหัสกิจกรรมต้องไม่เกิน 255 ตัวอักษร',

            'activitiydetailspendmoney.required' => 'กรุณาระบุรายระเอียดค่าใช้จ่าย',
            'activitiydetailspendmoney.array' => 'รายระเอียดค่าใช้จ่ายต้องเป็นรายการ',
        ];
    }
}
