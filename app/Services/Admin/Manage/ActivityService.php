<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\ActionPlan;
use App\Models\Activity;
use App\Models\ActivityPrinciple;
use App\Models\ActivitySpendmoney;
use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Models\IndicatorActivity;
use App\Models\Objective;
use App\Models\ObjectiveActivity;
use App\Models\OkrDetailActivity;
use App\Models\ProjectUser;
use App\Models\Project;
use App\Models\StyleActivtiyDetail;
use Illuminate\Notifications\Action;
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

    public function getdataspendprice($id)
    {
        $result = Activity::where('id_project', $id)
            ->whereNull('deleted_at')
            ->selectRaw('SUM(budget) as total_budget, SUM(spend_money) as total_spend')
            ->first();

        return   $result;
    }
    public function getByID($id)
    {
        $activity = Activity::with('department')
            // ->with('department')
            ->with('ObjectiveActivity')
            ->with('ActivityUsers.user')
            ->with('ActivityUsers.user.position')

            ->with('activityStyle')
            ->with('year')
            ->with('activityPrinciple')
            ->with('activityIndicator')
            ->with('activityIndicator.unit')
            ->with('activityOkr')
            ->with('activityspendmoney')
            ->with('activityspendmoney.unit')
            ->with('activityOkr.okr')
            ->with('activityOkr.okr.unit')
            ->whereNull('deleted_at')
            ->where('activity_id', $id)
            ->orderBy('id')
            ->get();
        return $activity;
    }
    public function getBudgetAll($idProject)
    {
        $project = Project::where('project_id', $idProject)
            ->lockForUpdate()
            ->firstOrFail();

        $projectBudget = (float) $project->budget;
        $usedBudget = (float) Activity::where('id_project', $project->project_id)->sum('budget');


        $data = [
            'budget_project' => $projectBudget,
            'used_budget'    => $usedBudget,
            'remaining'      => $projectBudget - $usedBudget,
        ];

        return $data;
    }
    public function getByIDforSendEmail($id)
    {
        return Activity::where('activity_id', $id)->first();
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


        $activity = Activity::with('department')
            ->where('id_project', $id)
            ->where('status', 1)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();
        return $activity;
    }
    public function getByIDactivityAdmin($id, $perPage)
    {
        $activities = Activity::with([
            'department',
            'ObjectiveActivity',
            'ActivityUsers.user',
            'ActivityUsers.user.position',
            'activityspendmoney',
            'activityspendmoney.unit',
            'activityspendmoney.ActivityDetailSpendmoney' => function ($q) {
                $q->whereNull('deleted_at');
            },
            'project',
            'project.actionplan',
            'project.actionplan.strategic',
            'activityStyle',
            'year',
            'activityPrinciple',
            'activityIndicator',
            'activityIndicator.unit',
            'activityOkr',
            'activityOkr.okr',
            'activityOkr.okr.unit',
        ])
            ->whereNull('deleted_at')
            ->where('id_project', $id)
            ->whereHas('activityspendmoney.ActivityDetailSpendmoney', function ($q) {
                $q->whereNull('deleted_at');
            })
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
            ->with('project')
            ->with('project.actionplan')
            ->with('project.actionplan.strategic')
            ->orderBy('activity_id')
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

            $project = Project::where('project_id', $projectDTO->idProject)
                ->lockForUpdate()
                ->firstOrFail();

            $projectBudget = (float) $project->budget;
            $newActivityBudget = (float) ($projectDTO->budget ?? 0);
            $usedBudget = (float) Activity::where('id_project', $project->project_id)->sum('budget');

            $newTotal = $usedBudget + $newActivityBudget;

            if ($newTotal > $projectBudget) {
                abort(
                    422,
                    'ยอดงบประมาณรวมของกิจกรรม (' . number_format($newTotal, 2) .
                        ') เกินงบประมาณของโครงการ (' . number_format(($newTotal - $projectBudget), 2) . ')'
                );
            }

            $actionPlanDTO = $projectDTO->actionPlanDTO;
            $actionPlanDB = ActionPlan::findOrFail($actionPlanDTO->actionPlanID);
            // $actionPlanDB->save();

            // Project
            $activityDB->name_activity = $projectDTO->nameActivity;
            // $activityDB->agency = $projectDTO->agency;
            $activityDB->abstract = $projectDTO->abstract;
            $activityDB->time_start = $projectDTO->timeStart;
            $activityDB->time_end = $projectDTO->timeEnd;
            $activityDB->location = $projectDTO->location;
            // $activityDB->id_action_plan = $actionPlanDTO->actionPlanID;
            $activityDB->budget = $projectDTO->budget;
            // $activityDB->OKR_id = "";
            // $activityDB->detail_short = "";
            $activityDB->spend_money = 0;
            $activityDB->id = $projectDTO->id;
            $activityDB->id_project = $projectDTO->idProject;

            $activityDB->id_department = $projectDTO->idDepartment;
            $activityDB->result = $projectDTO->result;
            $activityDB->id_year = $projectDTO->idYear;
            $activityDB->id_project = $projectDTO->idProject;
            $activityDB->obstacle = $projectDTO->obstacle;
            $activityDB->save();


            // Okr detail project
            $okrDetailProjectsDTO = $projectDTO->okrDetailProjectsDTO;
            foreach ($okrDetailProjectsDTO as $key => $value) {
                $okrDetailProjectDB = new OkrDetailActivity();
                $okrDetailProjectDB->id_activity = $activityDB->activity_id;
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
                $styleActivtiyDetailDB->id_activity = $activityDB->activity_id; //TODO ไม่มี id activity
                $styleActivtiyDetailDB->save();
            }

            // Objective
            $objectivesDTO = $projectDTO->ObjectivesDTO;
            foreach ($objectivesDTO as $key => $value) {
                $objectiveDB = new ObjectiveActivity();
                $objectiveDB->objective_activity_name = $value->objectiveName;
                $objectiveDB->id_activity =  $activityDB->activity_id;
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
                $indicatorDB->id_activity = $activityDB->activity_id;
                $indicatorDB->id_unit =  $value->idUnit;
                $indicatorDB->save();
            }


            // ActivitiySpendMoney
            $ActiivitySpendDTO = $projectDTO->ActivitiySpendMoneyDTO;
            foreach ($ActiivitySpendDTO as $key => $value) {
                $indicatorDB = new ActivitySpendmoney();
                $indicatorDB->activity_spendmoney_name =  $value->name;
                $indicatorDB->id_unit =  $value->idUnit;
                $indicatorDB->id_activity = $activityDB->activity_id;
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

    public function update(ActivityDTO $projectDTO, $id)
    {
        // ค้นหาโครงการที่ต้องการอัปเดต
        $projectDB = Activity::where('activity_id', $id)->firstOrFail();

        DB::transaction(function () use ($projectDTO, $projectDB, $id) {
            // Action plan
            // $actionPlanDTO = $projectDTO->actionPlanDTO;
            // $actionPlanDB = ActionPlan::findOrFail($actionPlanDTO->actionPlanID);

            // อัปเดตข้อมูลโครงการ
            $projectDB->name_activity = $projectDTO->nameActivity;
            $projectDB->id = $projectDTO->id;
            $projectDB->abstract = $projectDTO->abstract;
            $projectDB->time_start = $projectDTO->timeStart;
            $projectDB->time_end = $projectDTO->timeEnd;
            $projectDB->location = $projectDTO->location;
            $projectDB->budget = $projectDTO->budget;
            // $projectDB->id_action_plan = $actionPlanDTO->actionPlanID;  // แก้ไขให้ใช้ actionPlanID
            // $projectDB->detail_short = ""; // กรณีที่ไม่มีข้อมูลที่จะใส่
            // $projectDB->spend_money = 0; // ค่าคงที่เริ่มต้น

            $projectDB->id_department = $projectDTO->idDepartment;
            $projectDB->result = $projectDTO->result;
            $projectDB->id_year = $projectDTO->idYear;
            $projectDB->obstacle = $projectDTO->obstacle;


            // // เพิ่มข้อมูลใหม่
            // ----- เตรียมข้อมูล -----
            $activityId          = $id;
            $styleDetailsDTO    = $projectDTO->styleDetailsDTO;          // array ของ obj ที่มาจากฟอร์ม
            $incomingStyles     = collect($styleDetailsDTO)
                ->pluck('idStyle')                   // ดึง id_style ที่ส่งมา
                ->toArray();

            // ----- ข้อมูลที่มีอยู่ใน DB -----
            $existingStyles = StyleActivtiyDetail::where('id_activity', $activityId)
                ->pluck('id_style')
                ->toArray();

            // ===== 1) เพิ่มอันที่ยังไม่มี =====
            $toInsert = array_diff($incomingStyles, $existingStyles);

            foreach ($toInsert as $styleId) {
                StyleActivtiyDetail::create([
                    'id_activity' => $activityId,
                    'id_style'   => $styleId,
                ]);
            }

            // ===== 2) ลบอันที่ผู้ใช้เอาออก =====
            $toDelete = array_diff($existingStyles, $incomingStyles);

            StyleActivtiyDetail::where('id_activity', $activityId)
                ->whereIn('id_style', $toDelete)
                ->delete();
            // Principle

            $activityId = $id;
            $principlesDTO = $projectDTO->principlesDTO;

            // 1. ดึงรายการ principle_id ที่มีอยู่ใน DB
            $existingPrinciples = ActivityPrinciple::where('id_activity', $activityId)
                ->pluck('id_principle')
                ->toArray();

            // 2. ดึงรายการ principle_id ที่รับเข้ามาใหม่
            $incomingPrinciples = collect($principlesDTO)->pluck('namePriciples')->toArray();

            // 3. หาอันที่ "ยังไม่มี" → เพิ่มเข้าไป
            $toInsert = array_diff($incomingPrinciples, $existingPrinciples);
            foreach ($toInsert as $principleId) {
                ActivityPrinciple::create([
                    'id_activity'   => $activityId,
                    'id_principle' => $principleId,
                ]);
            }

            // 4. หาอันที่ "ไม่มีใน input แล้ว" → ลบออก
            $toDelete = array_diff($existingPrinciples, $incomingPrinciples);
            ActivityPrinciple::where('id_activity', $activityId)
                ->whereIn('id_principle', $toDelete)
                ->delete();
            // บันทึกการอัปเดต
            $projectDB->save();
        });

        return $projectDB;
    }
}
