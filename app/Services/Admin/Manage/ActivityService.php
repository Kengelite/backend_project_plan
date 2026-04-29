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
use App\Models\ActivityDetail;
use App\Models\StyleActivtiyDetail;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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
        $dataActivity = Activity::where('id_project', $id)
            ->whereNull('deleted_at')
            ->selectRaw('SUM(budget) as total_budget, SUM(spend_money) as total_spend')
            ->first();

        $dataProject = Project::select('budget')
            ->where('project_id', $id)
            ->first();
        return [
            'total_budget' => $dataActivity->total_budget,
            'total_spend'             => $dataActivity->total_spend,
            'strategic_budget'        => $dataProject->budget ?? 0,
            'remaining_budget'        => ($dataProject->budget ?? 0) - $dataActivity->total_budget,
        ];
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
        $query = Activity::with([
                // หน่วยงาน / ปี
                'department',
                'year',

                // โครงการ > กลยุทธ์ > ยุทธศาสตร์
                'project',
                'project.department',
                'project.year',
                'project.actionplan',
                'project.actionplan.strategic',

                // วัตถุประสงค์
                'ObjectiveActivity',

                // ผู้รับผิดชอบ
                'ActivityUsers',
                'ActivityUsers.user',
                'ActivityUsers.user.position',

                // ลักษณะโครงการ / ธรรมาภิบาล
                'activityStyle',
                'activityPrinciple',

                // ตัวชี้วัดกิจกรรม
                'activityIndicator',
                'activityIndicator.unit',

                // OKR
                'activityOkr',
                'activityOkr.okr',
                'activityOkr.okr.unit',

                // รายละเอียดงบประมาณ
                'activityspendmoney',
                'activityspendmoney.unit',
                'activityspendmoney.ActivityDetailSpendmoney' => function ($q) {
                    $q->whereNull('deleted_at');
                },
            ])
            ->withCount([
                'activityDetails as activity_detail_count' => function ($q) {
                    $q->whereNull('deleted_at');
                }
            ])
            ->where('id_project', $id)
            ->whereNull('deleted_at');

        // ถ้าไม่ใช่ superadmin ให้เห็นเฉพาะ activity ที่เปิด
        if (auth()->user()->role != 2 && auth()->user()->role !== 'superadmin') {
            $query->where('status', 1);
        }

        $activity = $query->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $activity->getCollection()->transform(function ($item) {
            $project = optional($item->project);
            $actionplan = optional($project->actionplan);
            $strategic = optional($actionplan->strategic);
            $department = optional($item->department);
            $projectDepartment = optional($project->department);
            $year = optional($item->year);
            $projectYear = optional($project->year);

            // -------------------------
            // project
            // -------------------------
            $item->project_id = $project->project_id;
            $item->id_project = $project->project_id;
            $item->project_number = $project->project_number;
            $item->project_name = $project->project_name;
            $item->project_budget = $project->budget;
            $item->project_spend_money = $project->spend_money;

            // -------------------------
            // action plan
            // -------------------------
            $item->action_plan_number = $actionplan->action_plan_number;
            $item->actionplan_number = $actionplan->action_plan_number;
            $item->action_plan_name = $actionplan->name_ap;
            $item->name_ap = $actionplan->name_ap;

            // -------------------------
            // strategic
            // -------------------------
            $item->strategic_number = $strategic->strategic_number;
            $item->strategic_name = $strategic->strategic_name;

            // -------------------------
            // department
            // ถ้า activity ไม่มี department ให้ fallback ไปใช้ project department
            // -------------------------
            $item->departments_name =
                $department->departments_name
                ?? $department->department_name
                ?? $projectDepartment->departments_name
                ?? $projectDepartment->department_name;

            $item->department_name =
                $department->department_name
                ?? $department->departments_name
                ?? $projectDepartment->department_name
                ?? $projectDepartment->departments_name;

            // -------------------------
            // year
            // -------------------------
            $item->year_name = $year->year ?? $projectYear->year;
            $item->budget_year = $year->year ?? $projectYear->year;

            // -------------------------
            // alias ให้ frontend อ่านง่าย
            // -------------------------
            $item->objective_activity = $item->ObjectiveActivity ?? [];
            $item->activity_users = $item->ActivityUsers ?? [];
            $item->activity_style = $item->activityStyle ?? [];
            $item->activity_principle = $item->activityPrinciple ?? [];
            $item->activity_indicator = $item->activityIndicator ?? [];
            $item->activity_okr = $item->activityOkr ?? [];
            $item->activityspendmoney = $item->activityspendmoney ?? [];

            // alias เผื่อ frontend อ่าน camelCase
            $item->objectiveActivity = $item->objective_activity;
            $item->activityUsers = $item->activity_users;
            $item->activityStyle = $item->activity_style;
            $item->activityPrinciple = $item->activity_principle;
            $item->activityIndicator = $item->activity_indicator;
            $item->activityOkr = $item->activity_okr;
            $item->activitySpendMoney = $item->activityspendmoney;

            return $item;
        });

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
            ->where('status', 1)
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $activities->getCollection()->transform(function ($activity) {
            $activity->activity_detail_count = DB::table('Activity_detail')
                ->where('id_activity', $activity->activity_id)
                ->whereNull('deleted_at')
                ->count();

            $activity->project_number = optional($activity->project)->project_number;
            $activity->action_plan_number = optional(optional($activity->project)->actionplan)->action_plan_number;
            $activity->strategic_number = optional(optional(optional($activity->project)->actionplan)->strategic)->strategic_number;

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
        $user = Auth::user();
        $userId = $user->id;

        $query = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->with([
                'activity',
                'activity.department',
                'activity.year',

                'activity.project',
                'activity.project.actionplan',
                'activity.project.actionplan.strategic',

                'activity.ObjectiveActivity',

                'activity.ActivityUsers',
                'activity.ActivityUsers.user',
                'activity.ActivityUsers.user.position',

                'activity.activityStyle',

                'activity.activityPrinciple',

                'activity.activityIndicator',
                'activity.activityIndicator.unit',

                'activity.activityOkr',
                'activity.activityOkr.okr',
                'activity.activityOkr.okr.unit',

                'activity.activityspendmoney',
                'activity.activityspendmoney.unit',
                'activity.activityspendmoney.ActivityDetailSpendmoney' => function ($q) {
                    $q->whereNull('deleted_at');
                },
            ]);

        // ถ้าไม่ใช่ superadmin ให้เห็นเฉพาะ activity ที่ status = 1
        // รองรับทั้งกรณี role เป็น 2 และเป็น string superadmin
        if ($user->role != 2 && $user->role !== 'superadmin') {
            $query->whereHas('activity', function ($q) {
                $q->where('status', 1);
            });
        }

        $activityUsers = $query->paginate($perPage);

        $activityUsers->getCollection()->transform(function ($item) {
            if ($item->activity) {
                $activity = $item->activity;

                $project = optional($activity->project);
                $actionplan = optional($project->actionplan);
                $strategic = optional($actionplan->strategic);

                // project
                $activity->project_number = $project->project_number;
                $activity->project_name = $project->project_name;
                $activity->id_project = $project->project_id;
                $activity->project_budget = $project->budget;
                $activity->project_spend_money = $project->spend_money;

                // action plan
                $activity->action_plan_number = $actionplan->action_plan_number;
                $activity->action_plan_name = $actionplan->name_ap;

                // strategic
                $activity->strategic_number = $strategic->strategic_number;
                $activity->strategic_name = $strategic->strategic_name;

                // count report
                $activity->activity_detail_count = ActivityDetail::where('id_activity', $activity->activity_id)
                    ->whereNull('deleted_at')
                    ->count();

                // ทำ alias เพิ่มให้ frontend อ่านง่าย
                $activity->objective_activity = $activity->ObjectiveActivity ?? [];
                $activity->activity_users = $activity->ActivityUsers ?? [];
                $activity->activity_style = $activity->activityStyle ?? [];
                $activity->activity_principle = $activity->activityPrinciple ?? [];
                $activity->activity_indicator = $activity->activityIndicator ?? [];
                $activity->activity_okr = $activity->activityOkr ?? [];
            }

            return $item;
        });

        return $activityUsers;
    }

    public function getByIDYear($id, $perPage)
    {
        $query = Activity::where('id_year', $id)
            ->with('project')
            ->with('project.actionplan')
            ->with('project.actionplan.strategic')
            ->withCount([
                'activityDetails as activity_detail_count' => function ($q) {
                    $q->whereNull('deleted_at');
                }
            ])
            ->whereNull('deleted_at');

        if (auth()->user()->role != 2) {
            $query->where('status', 1);
        }

        $project = $query->orderBy('activity_id')
            ->paginate($perPage)
            ->withQueryString();

        $project->getCollection()->transform(function ($activity) {
            $activity->project_number = optional($activity->project)->project_number;
            $activity->action_plan_number = optional(optional($activity->project)->actionplan)->action_plan_number;
            $activity->strategic_number = optional(optional(optional($activity->project)->actionplan)->strategic)->strategic_number;

            return $activity;
        });

        return $project;
    }

public function getResponsibleByProject($id_project, $perPage)
{
    $userId = Auth::id();

    $activity = Activity::with([
            'department',
            'project',
            'year',
            'actionplan',

            'ActivityUsers',
            'ActivityUsers.user',

            'ObjectiveActivity',

            'activityOkr',
            'activityOkr.okr',
            'activityOkr.okr.unit',

            'activityPrinciple',
            'activityStyle',

            'activityIndicator',
            'activityIndicator.unit',

            'activityspendmoney',
            'activityspendmoney.unit',
            'activityspendmoney.ActivityDetailSpendmoney' => function ($q) {
                $q->whereNull('deleted_at');
            },

            'activityDetails',
        ])
        ->withCount([
            'activityDetails as activity_detail_count' => function ($q) {
                $q->whereNull('deleted_at');
            }
        ])
        ->where('id_project', $id_project)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->whereHas('ActivityUsers', function ($query) use ($userId) {
            $query->where('id_user', $userId);
        })
        ->orderBy('id')
        ->paginate($perPage)
        ->withQueryString();

    $activity->getCollection()->transform(function ($item) {
        $project = optional($item->project);
        $actionplan = optional($project->actionplan ?: $item->actionplan);
        $strategic = optional($actionplan->strategic);
        $department = optional($item->department);
        $year = optional($item->year);

        $item->project_id = $project->project_id;
        $item->id_project = $project->project_id;
        $item->project_number = $project->project_number;
        $item->project_name = $project->project_name;

        $item->action_plan_number = $actionplan->action_plan_number;
        $item->actionplan_number = $actionplan->action_plan_number;
        $item->action_plan_name = $actionplan->name_ap;
        $item->name_ap = $actionplan->name_ap;

        $item->strategic_number = $strategic->strategic_number;
        $item->strategic_name = $strategic->strategic_name;

        $item->departments_name =
            $department->departments_name
            ?? $department->department_name
            ?? null;

        $item->department_name =
            $department->department_name
            ?? $department->departments_name
            ?? null;

        $item->year_name = $year->year;
        $item->budget_year = $year->year;

        // alias ให้ frontend อ่านได้
        $item->objective_activity = $item->ObjectiveActivity ?? [];
        $item->activity_users = $item->ActivityUsers ?? [];
        $item->activity_style = $item->activityStyle ?? [];
        $item->activity_principle = $item->activityPrinciple ?? [];
        $item->activity_indicator = $item->activityIndicator ?? [];
        $item->activity_okr = $item->activityOkr ?? [];
        $item->activityspendmoney = $item->activityspendmoney ?? [];

        // camelCase alias
        $item->objectiveActivity = $item->objective_activity;
        $item->activityUsers = $item->activity_users;
        $item->activityStyle = $item->activity_style;
        $item->activityPrinciple = $item->activity_principle;
        $item->activityIndicator = $item->activity_indicator;
        $item->activityOkr = $item->activity_okr;
        $item->activitySpendMoney = $item->activityspendmoney;

        return $item;
    });

    return $activity;
}

private function firstExistingTable(array $tables)
{
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            return $table;
        }
    }

    return null;
}

private function firstExistingColumn($table, array $columns)
{
    if (!$table) return null;

    foreach ($columns as $column) {
        if (Schema::hasColumn($table, $column)) {
            return $column;
        }
    }

    return null;
}

private function attachOkrDataForWord($activities)
{
    $okrIds = collect();

    foreach ($activities as $activity) {
        foreach ($activity->activityOkr ?? [] as $item) {
            $okrId = $item->id_okr ?? $item->OKR_id ?? $item->okr_id ?? null;

            if ($okrId) {
                $okrIds->push($okrId);
            }
        }
    }

    $okrIds = $okrIds->filter()->unique()->values();

    if ($okrIds->isEmpty()) {
        return;
    }

    $okrTable = $this->firstExistingTable([
        'OKR',
        'Okr',
        'okr',
        'okrs',
        'OKRs',
    ]);

    if (!$okrTable) {
        return;
    }

    $okrPk = $this->firstExistingColumn($okrTable, [
        'okr_id',
        'OKR_id',
        'id_okr',
        'id',
    ]);

    if (!$okrPk) {
        return;
    }

    $okrs = DB::table($okrTable)
        ->whereIn($okrPk, $okrIds)
        ->get()
        ->keyBy(function ($row) use ($okrPk) {
            return (string) $row->{$okrPk};
        });

    foreach ($activities as $activity) {
        foreach ($activity->activityOkr ?? [] as $item) {
            $okrId = $item->id_okr ?? $item->OKR_id ?? $item->okr_id ?? null;

            if ($okrId && isset($okrs[(string) $okrId])) {
                $item->setAttribute('okr', $okrs[(string) $okrId]);
            }
        }
    }
}

private function attachSpendMoneyDataForWord($activities)
{
    $activityDetailIds = collect();

    foreach ($activities as $activity) {
        foreach ($activity->activityDetails ?? [] as $detail) {
            $detailId = $detail->activity_detail_id ?? $detail->id ?? $detail->id_activity_detail ?? null;

            if ($detailId) {
                $activityDetailIds->push($detailId);
            }
        }
    }

    $activityDetailIds = $activityDetailIds->filter()->unique()->values();

    if ($activityDetailIds->isEmpty()) {
        return;
    }

    $spendTable = $this->firstExistingTable([
        'ActivitiyDetailSpendMoney',
        'ActivitiyDetailSpendmoney',
        'activity_detail_spendmoney',
        'activity_detail_spend_money',
        'ActivityDetailSpendMoney',
        'ActivityDetailSpendmoney',
        'activitiydetailspendmoney',
    ]);

    if (!$spendTable) {
        return;
    }

    $fk = $this->firstExistingColumn($spendTable, [
        'id_activity_detail',
        'activity_detail_id',
        'id_detail_activity',
    ]);

    if (!$fk) {
        return;
    }

    $spendRows = DB::table($spendTable)
        ->whereIn($fk, $activityDetailIds)
        ->get();

    $unitTable = $this->firstExistingTable([
        'Unit',
        'unit',
        'units',
    ]);

    $unitPk = $this->firstExistingColumn($unitTable, [
        'unit_id',
        'id_unit',
        'id',
    ]);

    $unitFk = $this->firstExistingColumn($spendTable, [
        'id_unit',
        'unit_id',
    ]);

    if ($unitTable && $unitPk && $unitFk) {
        $unitIds = $spendRows
            ->pluck($unitFk)
            ->filter()
            ->unique()
            ->values();

        $units = DB::table($unitTable)
            ->whereIn($unitPk, $unitIds)
            ->get()
            ->keyBy(function ($row) use ($unitPk) {
                return (string) $row->{$unitPk};
            });

        $spendRows = $spendRows->map(function ($row) use ($unitFk, $units) {
            $unitId = $row->{$unitFk} ?? null;

            if ($unitId && isset($units[(string) $unitId])) {
                $row->unit = $units[(string) $unitId];
            }

            return $row;
        });
    }

    $grouped = $spendRows->groupBy(function ($row) use ($fk) {
        return (string) $row->{$fk};
    });

    foreach ($activities as $activity) {
        foreach ($activity->activityDetails ?? [] as $detail) {
            $detailId = $detail->activity_detail_id ?? $detail->id ?? $detail->id_activity_detail ?? null;

            $detail->setAttribute(
                'activitiydetailspendmoney',
                $grouped->get((string) $detailId, collect())->values()->all()
            );
        }
    }
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

            // generate running id per project
            $nextRunningId = (int) Activity::where('id_project', $projectDTO->idProject)->max('id');
            $nextRunningId = $nextRunningId + 1;

            // generate uuid for activity_id
            $activityDB->activity_id = (string) Str::uuid();

            // important: id in your table is required
            $activityDB->id = $nextRunningId;

            $activityDB->name_activity = $projectDTO->nameActivity;
            $activityDB->abstract = $projectDTO->abstract;
            $activityDB->time_start = $projectDTO->timeStart;
            $activityDB->time_end = $projectDTO->timeEnd;
            $activityDB->location = $projectDTO->location;
            $activityDB->budget = $projectDTO->budget;
            $activityDB->spend_money = 0;
            $activityDB->id_project = $projectDTO->idProject;
            $activityDB->id_department = $projectDTO->idDepartment;
            $activityDB->result = $projectDTO->result;
            $activityDB->id_year = $projectDTO->idYear;
            $activityDB->obstacle = $projectDTO->obstacle;
            $activityDB->save();

            $okrDetailProjectsDTO = $projectDTO->okrDetailProjectsDTO;
            foreach ($okrDetailProjectsDTO as $key => $value) {
                $okrDetailProjectDB = new OkrDetailActivity();
                $okrDetailProjectDB->id_activity = $activityDB->activity_id;
                $okrDetailProjectDB->id_okr = $value->idOkr;
                $okrDetailProjectDB->save();
            }

            $principlesDTO = $projectDTO->principlesDTO;
            foreach ($principlesDTO as $key => $value) {
                $principleDB = new ActivityPrinciple();
                $principleDB->id_principle = $value->idPriciples;
                $principleDB->id_activity = $activityDB->activity_id;
                $principleDB->save();
            }

            $styleActivtiyDetailsDTO = $projectDTO->styleActivtiyDetailsDTO;
            foreach ($styleActivtiyDetailsDTO as $key => $value) {
                $styleActivtiyDetailDB = new StyleActivtiyDetail();
                $styleActivtiyDetailDB->id_style = $value->idStyle;
                $styleActivtiyDetailDB->id_activity = $activityDB->activity_id;
                $styleActivtiyDetailDB->save();
            }

            $objectivesDTO = $projectDTO->ObjectivesDTO;
            foreach ($objectivesDTO as $key => $value) {
                $objectiveDB = new ObjectiveActivity();
                $objectiveDB->objective_activity_name = $value->objectiveName;
                $objectiveDB->id_activity = $activityDB->activity_id;
                $objectiveDB->save();
            }

            $employeesDTO = $projectDTO->employeesDTO;
            foreach ($employeesDTO as $key => $value) {
                $projectUserDB = new ActivityUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_activity = $activityDB->activity_id;
                $projectUserDB->id_year = $projectDTO->idYear;
                $projectUserDB->save();
            }

            $teachersDTO = $projectDTO->teachersDTO;
            foreach ($teachersDTO as $key => $value) {
                $projectUserDB = new ActivityUser();
                $projectUserDB->type = $value->type;
                $projectUserDB->main = $value->main;
                $projectUserDB->id_user = $value->idUser;
                $projectUserDB->id_activity = $activityDB->activity_id;
                $projectUserDB->id_year = $projectDTO->idYear;
                $projectUserDB->save();
            }

            $indicatorsDTO = $projectDTO->indicatorsDTO;
            foreach ($indicatorsDTO as $key => $value) {
                $indicatorDB = new IndicatorActivity();
                $indicatorDB->indicator_name = $value->indicatorName;
                $indicatorDB->goal = $value->goal;
                $indicatorDB->id_activity = $activityDB->activity_id;
                $indicatorDB->id_unit = $value->idUnit;
                $indicatorDB->save();
            }

            $ActiivitySpendDTO = $projectDTO->ActivitiySpendMoneyDTO;
            foreach ($ActiivitySpendDTO as $key => $value) {
                $indicatorDB = new ActivitySpendmoney();
                $indicatorDB->activity_spendmoney_name = $value->name;
                $indicatorDB->id_unit = $value->idUnit;
                $indicatorDB->id_activity = $activityDB->activity_id;
                $indicatorDB->save();
            }
        });

        return $activityDB;
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
            $projectDB->id = $projectDTO->id;
            $projectDB->name_activity = $projectDTO->nameActivity;
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
