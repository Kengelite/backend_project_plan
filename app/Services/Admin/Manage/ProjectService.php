<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\Project;
use App\Trait\Utils;


class ProjectService
{
    use Utils;
    public function getAll()
    {
        $project = Project::paginate(10)->withQueryString();
        return $project;
    }
    public function getByID($id)
    {
        $project = Project::findOrFail($id);
        return $project;
    }
    public function getByIDactionplan($id)
    {
        $project = Project::where('id_action_plan',$id)
        ->orderBy('project_number')
        ->paginate(10)
        ->withQueryString();
        return $project;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $project = Project::where("project_id", $id);
        
        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $project->update([
            'status' => $project->first()->status == 0 ? 1 : 0
        ]);

    
        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $project;
    }


}
