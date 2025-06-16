<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\ActionPlan;
use App\Models\Activity;
use App\Models\ActivityPrinciple;
use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Models\IndicatorActivity;
use App\Models\Objective;
use App\Models\ObjectiveActivity;
use App\Models\OkrDetailActivity;
use App\Models\ProjectUser;
use App\Models\StyleActivtiyDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityService
{
    use Utils;
    public function getAll()
    {
        $activity = Activity::paginate(10)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Activity::findOrFail($id);
        return $activity;
    }

    public function updateSendEmailByID($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->count_send_email = ($activity->count_send_email ?? 0) + 1;
        $activity->save();
        return $activity;
    }

    public function getUserByIDActivity($id)
    {
        $activity = ActivityUser::with('user')
            ->where('id_activity', $id)
            ->get();

        return $activity;
    }


    public function getByIDactivity($id, $perPage)
    {


        $activity = Activity::where('id_project', $id)
            ->where('status', 1)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id, $perPage)
    {
        // $activity = DB::table('activity')
        // ->leftJoin('activity_detail', 'activity.activity_id', '=', 'activity_detail.id_activity')
        // ->select(
        //     'activity.activity_id',
        //     'activity.id',
        //     'activity.name_activity',
        //     'activity.budget',
        //     'activity.spend_money',
        //     'activity.status_performance',
        //     'activity.detail_performance',
        //     'activity.report_submission_status',
        //     'activity.agency',
        //     'activity.id_department',
        //     'activity.status',
        //     'activity.detail_short',
        //     'activity.id_project',
        //     'activity.id_year',
        //     'activity.status_report' ,
        //     DB::raw('COUNT(DISTINCT activity_detail.activity_detail_id) as activity_detail_count')
        // )
        // ->whereNull('activity.deleted_at')
        // ->whereNull('activity_detail.deleted_at')
        // ->where('activity.id_project', $id)
        // ->groupBy(
        //     'activity.activity_id',
        //     'activity.id',
        //     'activity.name_activity',
        //     'activity.budget',
        //     'activity.spend_money',
        //     'activity.status_performance',
        //     'activity.detail_performance',
        //     'activity.report_submission_status',
        //     'activity.agency',
        //     'activity.id_department',
        //     'activity.status',
        //     'activity.detail_short',
        //     'activity.id_project',
        //     'activity.id_year',
        //     'activity.status_report'
        // )
        // ->orderBy('activity.id')
        // ->paginate($perPage)
        // ->withQueryString();

        $activities = DB::table('activity')
            ->whereNull('deleted_at')
            ->where('id_project', $id)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        // นับจำนวน activity_detail ของแต่ละ activity แบบแยก

        // •	getCollection() → ดึงเฉพาะ array ของผลลัพธ์จาก paginator
        // •	transform(...) → ใช้เปลี่ยนค่าทุกแถวใน collection
        $activities->getCollection()->transform(function ($activity) {
            $activity->activity_detail_count = DB::table('activity_detail')
                ->where('id_activity', $activity->activity_id)
                ->whereNull('deleted_at')
                ->count();

            return $activity;
        });

        // $activity = Activity::where('id_project', $id)
        //     ->orderBy('id')
        //     ->paginate(10)
        //     ->withQueryString();
        return $activities;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $activity = Activity::where("activity_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $activity->update([
            'status' => $activity->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $activity;
    }

    public function getByIDUser($id, $perPage)
    {
        $userId = Auth::id();

        $activityUser = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->whereHas('activity', function ($query) {
                $query->where('status', 1);
            })
            ->with('activity')
            ->paginate($perPage);

        return $activityUser;
    }

    public function getByIDYear($id, $perPage)
    {

        $project = Activity::where('id_year', $id)
            ->orderBy('activity_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }

    public function sendEmail($id, $perPage)
    {

        $project = Activity::where('id_year', $id)
            ->orderBy('activity_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $activity = Activity::where('activity_id', $id)->firstOrFail();
        $activity->delete();
        return $activity;
    }


    public function store(ActivityDTO $projectDTO)
    {
        $activityDB = new Activity();

        DB::transaction(function () use ($projectDTO, $activityDB) {
            // Action plan
            $actionPlanDTO = $projectDTO->actionPlanDTO;
            $actionPlanDB = ActionPlan::findOrFail($actionPlanDTO->actionPlanID);
            // $actionPlanDB->save();

            // Project
            $activityDB->name_activity = $projectDTO->nameActivity;
            // $activityDB->agency = $projectDTO->agency;
            $activityDB->abstract = $projectDTO->abstract;
            // $activityDB->time_start = $projectDTO->timeStart;
            // $activityDB->time_end = $projectDTO->timeEnd;
            // $activityDB->location = $projectDTO->location;
            // $activityDB->id_action_plan = $actionPlanDTO->actionPlanID;
            // $activityDB->project_detail_id = "";
            // $activityDB->OKR_id = "";
            $activityDB->detail_short = "";
            $activityDB->spend_money = 0;
            $activityDB->id = $projectDTO->id;
            $activityDB->id_project = "";

            $activityDB->id_department = $projectDTO->idDepartment;
            $activityDB->result = $projectDTO->result;
            $activityDB->id_year = $projectDTO->idYear;
            $activityDB->obstacle = $projectDTO->obstacle;
            $activityDB->save();


            // Okr detail project
            $okrDetailProjectsDTO = $projectDTO->okrDetailProjectsDTO;
            foreach ($okrDetailProjectsDTO as $key => $value) {
                $okrDetailProjectDB = new OkrDetailActivity();
                $okrDetailProjectDB->id_acitivity = $activityDB->activity_id;
                $okrDetailProjectDB->id_okr = $value->idOkr;
                $okrDetailProjectDB->save();
            }

            // Principle
            $principlesDTO = $projectDTO->principlesDTO;
            foreach ($principlesDTO as $key => $value) {
                $principleDB = new ActivityPrinciple();
                // $principleDB->id_project = $activityDB->project_id;
                $principleDB->id_principle = $value->idPriciples;
                $principleDB->id_activity =  $activityDB->activity_id;
                // $principleDB->status = 0;
                $principleDB->save();
            }

            // Style Activtiy Detail
            $styleActivtiyDetailsDTO = $projectDTO->styleActivtiyDetailsDTO;
            foreach ($styleActivtiyDetailsDTO as $key => $value) {
                $styleActivtiyDetailDB = new StyleActivtiyDetail();
                $styleActivtiyDetailDB->id_style = $value->idStyle;
                $styleActivtiyDetailDB->id_activity = ""; //TODO ไม่มี id activity
                $styleActivtiyDetailDB->save();
            }

            // Objective
            $objectivesDTO = $projectDTO->ObjectivesDTO;
            foreach ($objectivesDTO as $key => $value) {
                $objectiveDB = new ObjectiveActivity();
                $objectiveDB->objective_activity_name = $value->objectiveName;
                $objectiveDB->objective_activity_id =  $activityDB->activity_id;
                $objectiveDB->save();
            }

            // Employee
            $employeesDTO = $projectDTO->employeesDTO;
            foreach ($employeesDTO as $key => $value) {
                $projectUserDB = new ActivityUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                // $projectUserDB->status = $value->status ?? "";
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_activity = $activityDB->activity_id;
                $projectUserDB->id_year = $value->idYear ?? 0;
                $projectUserDB->save();
            }

            // Teacher
            $teachersDTO = $projectDTO->teachersDTO;
            foreach ($teachersDTO as $key => $value) {
                $projectUserDB = new ActivityUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                // $projectUserDB->status = $value->status ?? "";
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_activity = $activityDB->activity_id;
                $projectUserDB->id_year = $value->idYear ?? 0;
                $projectUserDB->save();
            }

            // Indicator
            $indicatorsDTO = $projectDTO->indicatorsDTO;
            foreach ($indicatorsDTO as $key => $value) {
                $indicatorDB = new IndicatorActivity();
                $indicatorDB->indicator_name =  $value->indicatorName;
                $indicatorDB->goal =  $value->goal;
                // $indicatorDB->id_project = $activityDB->project_id;
                $indicatorDB->id_unit =  $value->idUnit;
                $indicatorDB->save();
            }


            // Result
            // $resultsDTO = $projectDTO->resultsDTO;
            // foreach ($resultsDTO as $key => $value) {
            //     $resultDB = new Result();
            //     $resultDB->name_result = $value->nameResult;
            //     $resultDB->id_project = $activityDB->project_id;
            //     $resultDB->status = 0;
            //     $resultDB->save();
            // }

            // Obstacle
            // $obstaclesDTO = $projectDTO->obstaclesDTO;
            // foreach ($obstaclesDTO as $key => $value) {
            //     $obstacleDB = new Obstacle();
            //     $obstacleDB->name_obstacle = $value->nameObstacle;
            //     $obstacleDB->status = 0;
            //     $obstacleDB->save();
            // }
        });

        return  $activityDB;
    }
}
