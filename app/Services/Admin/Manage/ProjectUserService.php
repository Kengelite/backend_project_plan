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

    public function getByIDYearDashborad($id)
    {
        $projectUsers = ProjectUser::where('id_year', $id)
            ->whereHas('project', function ($query) {
                $query->where('status', 1); // กรองจาก status ของ project
            })
            ->with('project') // โหลดข้อมูล project ที่เชื่อมกับ project_user
            ->get();

        $projectUsers->transform(function ($projectUser) {
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

    public function store($id_user, $id_project, $type, $id_year)
    {
        $projectDB = new ProjectUser();

        DB::transaction(function () use ($id_user, $id_project, $type, $id_year, &$projectDB) {
            $projectDB->id_user = $id_user;
            $projectDB->id_project = $id_project;
            $projectDB->type = $type;
            $projectDB->id_year = $id_year;
            $projectDB->main = 0;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $id_user)
    {

        $projectDB = ProjectUser::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $projectDB->id_user = $id_user;
        $projectDB->save();
        return $projectDB;
    }
    public function delete($id,$type)
    {
        $project = ProjectUser::where('id_project_user', $id)->firstOrFail();
        $isMain = $project->main == 1;
        $idProject = $project->id_project;

        $data =  DB::transaction(function () use ($project, $isMain, $idProject,$type) {
            $project->delete();

            if ($isMain) {
                $nextMainUser = ProjectUser::where('id_project', $idProject)
                    ->where('type',$type)
                    ->orderBy('created_at')
                    ->first();

                if ($nextMainUser) {
                    $nextMainUser->main = 1;
                    $nextMainUser->save();
                }
                // return $nextMainUser;
            }
            // return $isMain;
        });
        return $project;
    }
}
