<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\Obstacle;
use App\Models\OkrDetailProject;
use App\Models\Principle;
use App\Models\Project;
use App\Models\Result;
use App\Models\StyleDetail;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

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
    public function getByIDactionplan($id, $perPage)
    {
        // $project = Project::where('id_action_plan', $id)
        //     ->orderBy('project_number')
        //     ->paginate($perPage)
        //     ->withQueryString();

        //         $activity = DB::table('project')
        //             ->leftJoin('activity', 'project.project_id', '=', 'activity.id_project')
        //             ->select(
        //                 'project.project_id',
        //                 'project.project_number',
        //                 'project.project_name',
        //                 'project.budget',
        //                 'project.spend_money',
        //                 'project.status_performance',
        //                 'project.id_year',
        //                 'project.status',
        //                 'project.status_report',
        //                 DB::raw('COUNT(DISTINCT activity.activity_id) as count_activity'),
        //                 DB::raw("
        //   COUNT(CASE
        //     WHEN activity.status_report = 1
        //     THEN 1
        //     ELSE NULL
        //   END) AS count_activity_report
        // ")
        //             )
        //             ->whereNull('project.deleted_at')
        //             ->whereNull('activity.deleted_at')
        //             ->where('project.id_action_plan', $id)
        //             ->groupBy(
        //                 'project.project_id',
        //                 'project.project_number',
        //                 'project.project_name',
        //                 'project.budget',
        //                 'project.spend_money',
        //                 'project.status',
        //                 'project.status_performance',
        //                 'project.id_year',
        //                 'project.status_report',
        //             )
        //             ->orderBy('project.project_number')
        //             ->paginate($perPage)
        //             ->withQueryString();


        $projects = DB::table('project')
            ->whereNull('deleted_at')
            ->where('id_action_plan', $id)
            ->orderBy('project_number')
            ->paginate($perPage)
            ->withQueryString();


        // คำว่า clone ใน PHP (รวมถึงใน Laravel/Query Builder) หมายถึง การสร้างสำเนา (copy) ของ object เพื่อให้สามารถใช้งานหรือแก้ไขแยกจากต้นฉบับได้ โดยไม่กระทบกัน
        // เพิ่ม count_activity และ count_activity_report แบบแยก query
        // ช้ .getCollection() ดึง Collection ที่อยู่ใน paginator ออกมา เพื่อวนทำงานกับแต่ละแถว
        $projects->getCollection()->transform(function ($project) {
            // A. เตรียม query ของ activity ที่เกี่ยวข้องกับ project นี้
            $activitiesQuery = DB::table('activity')
                ->where('id_project', $project->project_id)
                ->whereNull('deleted_at');

            // B. นับจำนวนทั้งหมดของ activity ที่ผูกกับ project นี้
            $project->count_activity = (clone $activitiesQuery)->count();
            // C. นับเฉพาะ activity ที่ status_report = 1 (รายงานแล้ว)
            $project->count_activity_report = (clone $activitiesQuery)
                ->where('status_report', 1)
                ->count();

            return $project;
        });
        return $projects;
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


    public function getByIDYear($id, $perPage)
    {

        $project = Project::where('id_year', $id)
            ->orderBy('project_number')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }


    public function store(ProjectDTO $projectDTO)
    {
        $projectDB = new Project();

        DB::transaction(function () use ($projectDTO, $projectDB) {
            // Action plan
            $actionPlanDTO = $projectDTO->actionPlanDTO;
            $actionPlanDB = ActionPlan::findOrFail($actionPlanDTO->actionPlanID);
            // $actionPlanDB->save();

            // Project
            $projectDB->project_name = $projectDTO->projectName;
            $projectDB->agency = $projectDTO->agency;
            $projectDB->abstract = $projectDTO->abstract;
            $projectDB->time_start = $projectDTO->timeStart;
            $projectDB->time_end = $projectDTO->timeEnd;
            $projectDB->location = $projectDTO->location;
            $projectDB->id_action_plan = $actionPlanDTO->actionPlanID;
            $projectDB->project_detail_id = "";
            $projectDB->OKR_id = "";
            $projectDB->detail_short = "";
            $projectDB->spend_money = 0;
            $projectDB->save();

            // Style Detail
            $styleDetailsDTO = $projectDTO->styleDetailsDTO;
            foreach ($styleDetailsDTO as $key => $value) {
                $styleDetailDB = new StyleDetail();
                $styleDetailDB->id_project = $projectDB->project_id;
                $styleDetailDB->id_style = $value->idStyle;
                $styleDetailDB->save();
            }

            // Okr detail project
            $okrDetailProjectsDTO = $projectDTO->okrDetailProjectsDTO;
            foreach ($okrDetailProjectsDTO as $key => $value) {
                $okrDetailProjectDB = new OkrDetailProject();
                $okrDetailProjectDB->id_project = $projectDB->project_id;
                $okrDetailProjectDB->id_okr = $value->idOkr;
                $okrDetailProjectDB->save();
            }

            // Principle
            $principlesDTO = $projectDTO->principlesDTO;
            foreach ($principlesDTO as $key => $value) {
                $principleDB = new Principle();
                $principleDB->id_project = $projectDB->project_id;
                $principleDB->name_priciples = $value->namePriciples;
                $principleDB->status = 0;
                $principleDB->save();
            }

            // Result
            $resultsDTO = $projectDTO->resultsDTO;
            foreach ($resultsDTO as $key => $value) {
                $resultDB = new Result();
                $resultDB->name_result = $value->nameResult;
                $resultDB->id_project = $projectDB->project_id;
                $resultDB->status = 0;
                $resultDB->save();
            }

            // Obstacle
            $obstaclesDTO = $projectDTO->obstaclesDTO;
            foreach ($obstaclesDTO as $key => $value) {
                $obstacleDB = new Obstacle();
                $obstacleDB->name_obstacle = $value->nameObstacle;
                $obstacleDB->status = 0;
                $obstacleDB->save();
            }
        });

        return  $projectDB;
    }

    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = Project::where('project_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
