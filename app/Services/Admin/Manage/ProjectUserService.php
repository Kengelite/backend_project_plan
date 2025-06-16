<?php

namespace App\Services\Admin\Manage;  // คำสั่ง namespace ต้องอยู่บรรทัดแรก

use App\Trait\Utils;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectUserService
{
    use Utils;

    public function getAll()
    {
        $projectUsers = ProjectUser::with('project') // โหลดข้อมูล project ที่เชื่อมกับ project_user
            ->paginate(10);

        return $projectUsers;
    }

    public function getByIDUser()
    {
        $userId = Auth::id();
        $latestYear = Year::orderByDesc('year')->first();

        $projectUsers = ProjectUser::where('id_user', $userId)
            ->where('id_year', $latestYear->year_id)
            ->with('project') // โหลดข้อมูล project ที่เชื่อมกับ project_user
            ->paginate(10);

        return $projectUsers;
    }
    public function getByIDYear($id, $perPage)
    {
        $userId = Auth::id();

        $projectUsers = ProjectUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->whereHas('project', function ($query) {
                $query->where('status', 1); // กรองจาก status ของ project
            })
            ->with('project') // โหลดข้อมูล project ที่เชื่อมกับ project_user
            ->paginate($perPage);

        $projectUsers->getCollection()->transform(function ($projectUser) {
            // A. ดึง project ที่โหลดมาจาก with('project')
            $project = $projectUser->project;

            if ($project) {
                $activitiesQuery = DB::table('activity')
                    ->where('id_project', $project->id) // ใช้ $project->id แทน $projectUser->project_id
                    ->whereNull('deleted_at');

                // B. นับจำนวน activity ทั้งหมด
                $project->count_activity = (clone $activitiesQuery)->count();

                // C. นับเฉพาะที่รายงานแล้ว
                $project->count_activity_report = (clone $activitiesQuery)
                    ->where('status_report', 1)
                    ->count();
            }

            return $projectUser;
        });
        return $projectUsers;
    }
    public function getByIDYearAdmin($id)
    {
        $userId = Auth::id();

        $projectUsers = ProjectUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->with('project') // โหลดข้อมูล project ที่เชื่อมกับ project_user
            ->paginate(10);

        return $projectUsers;
    }
}
