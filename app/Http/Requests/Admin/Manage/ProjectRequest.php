<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'project_name' => 'required|string|max:255',
            'agency' => 'required|string|max:255',
            'abstract' => 'required|string',
            'id_actionplan' => 'required|string',
            'time_start' => 'required|date',
            'time_end' => 'required|date|after_or_equal:time_start',
            'location' => 'required|string|max:255',

            'style_detail' => 'required|array',
            'style_detail.*' => 'integer',

            'okr_detail_project' => 'required|array',
            'okr_detail_project.*' => 'string',

            'principles' => 'required|array',
            'principles.*' => 'string|max:255',

            'result' => 'required|array',
            'result.*' => 'string|max:255',

            'obstacle' => 'required|array',
            'obstacle.*' => 'string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'project_name.required' => 'กรุณากรอกชื่อโครงการ',
            'project_name.string' => 'ชื่อโครงการต้องเป็นข้อความ',
            'project_name.max' => 'ชื่อโครงการต้องไม่เกิน 255 ตัวอักษร',

            'agency.required' => 'กรุณากรอกชื่อหน่วยงาน',
            'agency.string' => 'ชื่อหน่วยงานต้องเป็นข้อความ',
            'agency.max' => 'ชื่อหน่วยงานต้องไม่เกิน 255 ตัวอักษร',

            'abstract.required' => 'กรุณากรอกบทคัดย่อ',
            'abstract.string' => 'บทคัดย่อต้องเป็นข้อความ',

            'id_actionplan.required' => 'กรุณาระบุแผนปฏิบัติการ',
            'id_actionplan.string' => 'แผนปฏิบัติการต้องเป็นข้อความ',

            'time_start.required' => 'กรุณาระบุวันเริ่มต้น',
            'time_start.date' => 'วันเริ่มต้นต้องเป็นวันที่ถูกต้อง',

            'time_end.required' => 'กรุณาระบุวันสิ้นสุด',
            'time_end.date' => 'วันสิ้นสุดต้องเป็นวันที่ถูกต้อง',
            'time_end.after_or_equal' => 'วันสิ้นสุดต้องเป็นวันเดียวกันหรือหลังจากวันเริ่มต้น',

            'location.required' => 'กรุณาระบุสถานที่',
            'location.string' => 'สถานที่ต้องเป็นข้อความ',
            'location.max' => 'สถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'style_detail.required' => 'กรุณาเลือกลักษณะของโครงการ',
            'style_detail.array' => 'รูปแบบของลักษณะโครงการไม่ถูกต้อง',
            'style_detail.*.integer' => 'ค่าลักษณะโครงการแต่ละรายการต้องเป็นตัวเลข',

            'okr_detail_project.required' => 'กรุณาระบุ OKR ของโครงการ',
            'okr_detail_project.array' => 'ข้อมูล OKR ของโครงการไม่ถูกต้อง',
            'okr_detail_project.*.string' => 'OKR ต้องเป็นอักษร',

            'principles.required' => 'กรุณาระบุหลักการของโครงการ',
            'principles.array' => 'รูปแบบหลักการไม่ถูกต้อง',
            'principles.*.string' => 'แต่ละหลักการต้องเป็นข้อความ',
            'principles.*.max' => 'แต่ละหลักการต้องไม่เกิน 255 ตัวอักษร',

            'result.required' => 'กรุณาระบุผลลัพธ์ของโครงการ',
            'result.array' => 'รูปแบบผลลัพธ์ไม่ถูกต้อง',
            'result.*.string' => 'แต่ละผลลัพธ์ต้องเป็นข้อความ',
            'result.*.max' => 'แต่ละผลลัพธ์ต้องไม่เกิน 255 ตัวอักษร',

            'obstacle.required' => 'กรุณาระบุอุปสรรคของโครงการ',
            'obstacle.array' => 'รูปแบบอุปสรรคไม่ถูกต้อง',
            'obstacle.*.string' => 'แต่ละอุปสรรคต้องเป็นข้อความ',
            'obstacle.*.max' => 'แต่ละอุปสรรคต้องไม่เกิน 255 ตัวอักษร',
        ];
    }
}
