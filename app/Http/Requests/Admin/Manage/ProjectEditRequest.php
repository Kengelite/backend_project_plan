<?php

namespace App\Http\Requests\Admin\Manage;

use Illuminate\Foundation\Http\FormRequest;

class ProjectEditRequest extends FormRequest
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
            'id_department' => 'required|string',
            'project_name' => 'required|string|max:255',
            'id_strategic' => 'required|string',
            'id_actionplan' => 'required|string',
            'budget' => 'required|string',
            'location' => 'required|string|max:255',
            'time_start' => 'required|date',
            'time_end' => 'required|date|after_or_equal:time_start',
            'abstract' => 'required|string',
            'result' => 'required|string',
            'obstacle' => 'required|string',
            'style_detail' => 'required|array',
            'style_detail.*' => 'integer',
            'okr_detail_project' => 'required|array',
            'okr_detail_project.*.id' => 'required|string',
            'project_principle' => 'required|array',
            'project_principle.*' => 'string|max:255',
            'id_year' => 'required|integer',

        ];
    }
    public function messages()
    {
        return [
            'id_department.required' => 'กรุณาเลือกหน่วยงาน',
            'id_department.string' => 'รหัสหน่วยงานต้องเป็นข้อความ',

            'project_name.required' => 'กรุณากรอกชื่อโครงการ',
            'project_name.string' => 'ชื่อโครงการต้องเป็นข้อความ',
            'project_name.max' => 'ชื่อโครงการต้องไม่เกิน 255 ตัวอักษร',

            'id_strategic.required' => 'กรุณาระบุยุทธศาสตร์',
            'id_strategic.string' => 'รหัสยุทธศาสตร์ต้องเป็นข้อความ',

            'id_actionplan.required' => 'กรุณาระบุแผนปฏิบัติการ',
            'id_actionplan.string' => 'แผนปฏิบัติการต้องเป็นข้อความ',

            'budget.required' => 'กรุณาระบุงบประมาณ',
            'budget.string' => 'งบประมาณต้องเป็นข้อความ',

            'location.required' => 'กรุณาระบุสถานที่ดำเนินโครงการ',
            'location.string' => 'สถานที่ต้องเป็นข้อความ',
            'location.max' => 'สถานที่ต้องไม่เกิน 255 ตัวอักษร',

            'time_start.required' => 'กรุณาระบุวันเริ่มต้น',
            'time_start.date' => 'วันเริ่มต้นต้องเป็นวันที่ถูกต้อง',

            'time_end.required' => 'กรุณาระบุวันสิ้นสุด',
            'time_end.date' => 'วันสิ้นสุดต้องเป็นวันที่ถูกต้อง',
            'time_end.after_or_equal' => 'วันสิ้นสุดต้องไม่ก่อนวันเริ่มต้น',

            'abstract.required' => 'กรุณากรอกบทคัดย่อ',
            'abstract.string' => 'บทคัดย่อต้องเป็นข้อความ',

            'result.required' => 'กรุณาระบุผลลัพธ์ของโครงการ',
            'result.string' => 'ผลลัพธ์ต้องเป็นข้อความ',

            'obstacle.required' => 'กรุณาระบุอุปสรรคของโครงการ',
            'obstacle.string' => 'อุปสรรคต้องเป็นข้อความ',


            'id_year.required' => 'กรุณาระบุปีงบประมาณ',
            'id_year.integer' => 'ปีงบประมาณต้องเป็นตัวเลข',


            'project_principle.required' => 'กรุณาระบุหลักการของโครงการ',
            'project_principle.array' => 'รูปแบบหลักการไม่ถูกต้อง',
            'project_principle.*.string' => 'แต่ละหลักการต้องเป็นข้อความ',
            'project_principle.*.max' => 'แต่ละหลักการต้องไม่เกิน 255 ตัวอักษร',
            
            'style_detail.required' => 'กรุณาเลือกลักษณะของโครงการ',
            'style_detail.array' => 'รูปแบบของลักษณะโครงการไม่ถูกต้อง',
            'style_detail.*.integer' => 'ค่าลักษณะโครงการแต่ละรายการต้องเป็นตัวเลข',

        ];
    }
}
