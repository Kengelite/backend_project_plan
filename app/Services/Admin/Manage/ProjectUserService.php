<?php

namespace App\Services\Admin\Manage;  // คำสั่ง namespace ต้องอยู่บรรทัดแรก

use App\Trait\Utils;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\ActionPlan;
use App\Models\Strategic;
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

        $projectUsers->getCollection()->transform(function ($project) {

            // ใช้ id_project จาก ProjectUser (ค่าตรงกับ project.project_id)
            $projectId = $project->id_project;

            // ชื่อตารางลองเช็คอีกที ถ้าตารางจริงคือ activities ให้เปลี่ยนเป็น 'activities'
            $activitiesQuery = DB::table('Activity')
                ->where('id_project', $projectId)
                ->whereNull('deleted_at');

            $project->count_activity = (clone $activitiesQuery)->count();

            $project->count_activity_report = (clone $activitiesQuery)
                ->where('status_report', 1)
                ->count();

            return $project;
        });
        return $projectUsers;
    }
    
public function getByIDYear($id, $perPage)
{
    $userId = Auth::id();

    $projectUsers = ProjectUser::where('id_user', $userId)
        ->where('id_year', $id)
        ->whereHas('project', function ($query) {
            $query->where('status', 1)
                ->whereNull('deleted_at');
        })
        ->with([
            // ข้อมูลโครงการหลัก
            'project',
            'project.department',
            'project.year',

            // แผนกลยุทธ์ / ยุทธศาสตร์
            'project.actionplan',
            'project.actionplan.strategic',

            // วัตถุประสงค์
            'project.Objective',

            // ลักษณะโครงการ
            'project.projectStyle',

            // หลักธรรมาภิบาล
            'project.projectPrinciple',

            // ตัวชี้วัดและค่าเป้าหมายของโครงการ
            'project.projectIndicator',
            'project.projectIndicator.unit',

            // OKR
            'project.projectOkr',
            'project.projectOkr.okr',
            'project.projectOkr.okr.unit',

            // ผู้รับผิดชอบ
            'project.projectUsers',
            'project.projectUsers.user',
            'project.projectUsers.user.position',

            // กิจกรรม + รายละเอียดงบประมาณของกิจกรรม
            'project.activity' => function ($q) {
                $q->whereNull('deleted_at')
                    ->where('status', 1);
            },
            'project.activity.activityspendmoney',
            'project.activity.activityspendmoney.unit',
            'project.activity.activityspendmoney.ActivityDetailSpendmoney' => function ($q) {
                $q->whereNull('deleted_at');
            },
        ])
        ->paginate($perPage)
        ->withQueryString();

    $projectUsers->getCollection()->transform(function ($projectUser) {
        $project = $projectUser->project;

        if ($project) {
            $activitiesQuery = DB::table('activity')
                ->where('id_project', $project->project_id)
                ->whereNull('deleted_at')
                ->where('status', 1);

            $project->count_activity = (clone $activitiesQuery)->count();

            $project->count_activity_report = (clone $activitiesQuery)
                ->where('status_report', 1)
                ->count();

            // -------------------------
            // action plan
            // -------------------------
            $actionplan = optional($project->actionplan);

            $project->action_plan_number = $actionplan->action_plan_number;
            $project->actionplan_number = $actionplan->action_plan_number;
            $project->action_plan_name = $actionplan->name_ap;
            $project->name_ap = $actionplan->name_ap;
            $project->id_action_plan = $actionplan->action_plan_id;

            // -------------------------
            // strategic
            // -------------------------
            $strategic = optional($actionplan->strategic);

            $project->id_strategic = $actionplan->id_strategic;
            $project->strategic_number = $strategic->strategic_number;
            $project->strategic_name = $strategic->strategic_name;

            // -------------------------
            // department
            // -------------------------
            $department = optional($project->department);

            $project->departments_name =
                $department->departments_name
                ?? $department->department_name
                ?? $department->name;

            $project->department_name =
                $department->department_name
                ?? $department->departments_name
                ?? $department->name;

            // -------------------------
            // year
            // -------------------------
            $year = optional($project->year);

            $project->year_name = $year->year;
            $project->budget_year = $year->year;

            // -------------------------
            // alias ให้ frontend / Word อ่านง่าย
            // -------------------------
            $project->objective_project = $project->Objective ?? [];
            $project->project_style = $project->projectStyle ?? [];
            $project->project_principle = $project->projectPrinciple ?? [];
            $project->project_indicator = $project->projectIndicator ?? [];
            $project->project_okr = $project->projectOkr ?? [];
            $project->project_users = $project->projectUsers ?? [];
            $project->activities = $project->activity ?? [];

            // alias camelCase เผื่อ frontend อ่านอีกแบบ
            $project->objectiveProject = $project->objective_project;
            $project->projectStyle = $project->project_style;
            $project->projectPrinciple = $project->project_principle;
            $project->projectIndicator = $project->project_indicator;
            $project->projectOkr = $project->project_okr;
            $project->projectUsers = $project->project_users;

            // -------------------------
            // รหัสโครงการแบบที่ต้องการ
            // strategicNumber-actionPlanNumber-projectNumber
            // -------------------------
            $strategicNumber = $project->strategic_number ?? '';
            $actionPlanNumber = $project->action_plan_number ?? '';
            $projectNumber = $project->project_number ?? '';

            $project->full_project_code = trim(
                $strategicNumber . '-' . $actionPlanNumber . '-' . $projectNumber,
                '-'
            );
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
    public function getByIDYearAdmin($id, $perPage)
    {
        $userId = Auth::id();

        $projectUsers = ProjectUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->with('project.actionplan.strategic')
            ->paginate($perPage);

        $projectUsers->getCollection()->transform(function ($projectUser) {
            $project = $projectUser->project;

            if ($project) {
                $activitiesQuery = DB::table('activity')
                    ->where('id_project', $project->project_id)
                    ->whereNull('deleted_at');

                $project->count_activity = (clone $activitiesQuery)->count();
                $project->count_activity_report = (clone $activitiesQuery)
                    ->where('status_report', 1)
                    ->count();

                // ดึงข้อมูล actionplan และ strategic
                $project->action_plan_number = optional($project->actionplan)->action_plan_number;
                $project->strategic_number = optional(optional($project->actionplan)->strategic)->strategic_number;
            }

            return $projectUser;
        });

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
    public function delete($id, $type)
    {
        $project = ProjectUser::where('id_project_user', $id)->firstOrFail();
        $isMain = $project->main == 1;
        $idProject = $project->id_project;

        $data =  DB::transaction(function () use ($project, $isMain, $idProject, $type) {
            $project->delete();

            if ($isMain) {
                $nextMainUser = ProjectUser::where('id_project', $idProject)
                    ->where('type', $type)
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
