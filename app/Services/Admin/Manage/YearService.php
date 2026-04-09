<?php

namespace App\Services\Admin\Manage;

use App\Dto\YearDTO;
use App\Models\Strategic;
use App\Models\Year;
use App\Trait\Utils;
use App\Models\ActionPlan;
use App\Models\Project;
use App\Models\Okr;
use App\Models\Activity;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class YearService
{
    use Utils;
    public function getAll()
    {
        $year = Year::orderByDesc('year')->paginate(10)->withQueryString();;
        return $year;
    }

    public function getAllYearUser()
    {
        $year = Year::where('status', 1)
            ->orderByDesc('year')
            ->paginate(10)->withQueryString();;
        return $year;
    }
    public function getYearAllNew($yearclient)
    {
        $id_year = Year::whereNull('deleted_at')
            ->where('year_id', '<', $yearclient)
            ->orderByDesc('year_id')
            ->value('year_id');


        // return $id_year;
        if (!$id_year) {
            return collect(); // กัน error กรณีมีปีเดียว
        }

        $dataYearOld = Strategic::select('strategic_id', 'id_year', 'strategic_name', 'strategic_number', 'fk_strategic')
            ->where('id_year', $id_year)
            ->with([
                'actionplan' => fn($q) =>
                $q->where('id_year', $id_year)
                    ->select('id_strategic', 'action_plan_id', 'id_year', 'name_ap', 'action_plan_number'),

                'actionplan.projects' => fn($q) =>
                $q->where('id_year', $id_year)
                    ->select('id_action_plan', 'project_id', 'id_year', 'project_name', 'project_number'),

                'actionplan.projects.activity' => fn($q) =>
                $q->where('id_year', $id_year)
                    ->select('id_project', 'id_year', 'name_activity', 'activity_id'),
            ])
            ->orderByDesc('strategic_id')
            ->get();

        $dataYearStrategic = Strategic::where('id_year', $yearclient)
            ->pluck('fk_strategic')
            ->toArray();


        $dataYearActionplan = ActionPlan::where('id_year', $yearclient)
            ->pluck('action_plan_id')
            ->toArray();


        $dataYearProject = Project::where('id_year', $yearclient)
            ->pluck('project_id')
            ->toArray();


        $dataYearActivity = Activity::where('id_year', $yearclient)
            ->pluck('activity_id')
            ->toArray();

        return [
            'dataYearOld' => $dataYearOld,
            'dataYearStrategic' => $dataYearStrategic,
            'dataYearActionplan' => $dataYearActionplan,
            'dataYearProject' => $dataYearProject,
            'dataYearActivity' => $dataYearActivity,
            'id_year' => $id_year
        ];
    }

    public function insertDatanewYear($sourceYearId, $targetYearId)
    {
        return DB::transaction(function () use ($sourceYearId, $targetYearId) {

            // 1. ดึงข้อมูลปีเป้าหมายมาเตรียมไว้
            $year = Year::findOrFail($targetYearId);
            $yearValue = (int) $year->year;
            $timeStart = Carbon::create($yearValue, 10, 1)->startOfDay();
            $timeEnd   = Carbon::create($yearValue + 1, 9, 30)->endOfDay();

            $index_s = 1;
            $index_ap = 1;
            $index_p = 1;
            $index_a = 1;

            /** =========================
             * 1. ดึง Strategic ของปีเก่า
             * ========================= */
            $oldStrategics = Strategic::where('id_year', $sourceYearId)->get();

            foreach ($oldStrategics as $s) {

                $newS = $s->replicate();
                $newS->id_year = $targetYearId; // เปลี่ยนเป็นปีใหม่
                $newS->strategic_number = "CPa" . $index_s++;
                $newS->fk_strategic =  $s->strategic_id;
                $newS->spend_money = 0;
                $newS->save();

                /** =========================
                 * 2. ดึง ActionPlan ที่ผูกกับ Strategic เก่า
                 * ========================= */
                $oldActionPlans = ActionPlan::where('id_strategic', $s->strategic_id)->get();

                foreach ($oldActionPlans as $ap) {

                    $newAp = $ap->replicate();
                    $newAp->id_strategic = $newS->strategic_id; // ผูกกับ Strategic ใหม่
                    $newAp->id_year = $targetYearId;
                    $newAp->action_plan_number = $index_ap++;
                    $newAp->spend_money = 0;
                    $newAp->save();

                    /** =========================
                     * 3. ดึง Project ที่ผูกกับ ActionPlan เก่า
                     * ========================= */
                    $oldProjects = Project::where('id_action_plan', $ap->action_plan_id)->get();

                    foreach ($oldProjects as $p) {

                        $newP = $p->replicate();
                        $newP->id_action_plan = $newAp->action_plan_id; // ผูกกับ ActionPlan ใหม่
                        $newP->id_year        = $targetYearId;
                        $newP->project_number = $index_p++;
                        $newP->status_report  = 0;
                        $newP->spend_money    = 0;
                        $newP->time_start     = $timeStart;
                        $newP->time_end       = $timeEnd;
                        $newP->save();

                        // Copy ความสัมพันธ์ของ Project
                        foreach ($p->users as $user) {
                            $newP->users()->attach($user->id, [
                                'type'    => $user->pivot->type,
                                'main'    => $user->pivot->main,
                                'status'  => $user->pivot->status,
                                'id_year' => $targetYearId,
                            ]);
                        }
                        foreach ($p->projectPrinciple as $pp) {
                            $newPP = $pp->replicate();
                            $newPP->id_project = $newP->project_id;
                            $newPP->save();
                        }
                        foreach ($p->Objective as $obj) {
                            $newObj = $obj->replicate();
                            $newObj->id_project = $newP->project_id;
                            $newObj->save();
                        }
                        foreach ($p->projectStyle as $style) {
                            $newStyle = $style->replicate();
                            $newStyle->id_project = $newP->project_id;
                            $newStyle->save();
                        }
                        foreach ($p->projectIndicator as $indicator) {
                            $newIndicator = $indicator->replicate();
                            $newIndicator->id_project = $newP->project_id;
                            $newIndicator->save();
                        }
                        foreach ($p->projectOkr as $okr) {
                            $newOkr = $okr->replicate();
                            $newOkr->id_project = $newP->project_id;
                            $newOkr->save();
                        }

                        /** =========================
                         * 4. ดึง Activity ที่ผูกกับ Project เก่า
                         * ========================= */
                        $oldActivities = Activity::where('id_project', $p->project_id)->get();

                        foreach ($oldActivities as $a) {

                            $newA = $a->replicate();
                            $newA->id_project       = $newP->project_id; // ผูกกับ Project ใหม่
                            $newA->id_year          = $targetYearId;
                            $newA->id               = $index_a++;
                            $newA->status_report    = 0;
                            $newA->spend_money      = 0;
                            $newA->actual_money     = 0;
                            $newA->count_send_email = 0;
                            $newA->time_start       = $timeStart;
                            $newA->time_end         = $timeEnd;
                            $newA->save();

                            // Copy ความสัมพันธ์ของ Activity
                            foreach ($a->ActivityUsers as $style) {
                                $newAu = $style->replicate();
                                $newAu->id_activity = $newA->activity_id;
                                $newAu->save();
                            }
                            foreach ($a->activityStyle as $style) {
                                $newStyle = $style->replicate();
                                $newStyle->id_activity = $newA->activity_id;
                                $newStyle->save();
                            }
                            foreach ($a->activityIndicator as $indicator) {
                                $newIndicator = $indicator->replicate();
                                $newIndicator->id_activity = $newA->activity_id;
                                $newIndicator->save();
                            }
                            foreach ($a->activityPrinciple as $pp) {
                                $newPP = $pp->replicate();
                                $newPP->id_activity = $newA->activity_id;
                                $newPP->save();
                            }
                            foreach ($a->ObjectiveActivity as $obj) {
                                $newObj = $obj->replicate();
                                $newObj->id_activity = $newA->activity_id;
                                $newObj->save();
                            }
                            foreach ($a->activityOkr as $okr) {
                                $newOkr = $okr->replicate();
                                $newOkr->id_activity = $newA->activity_id;
                                $newOkr->save();
                            }
                            foreach ($a->activityspendmoney as $okr) {
                                $newOkr = $okr->replicate();
                                $newOkr->id_activity = $newA->activity_id;
                                $newOkr->save();
                            }
                        } // End Activity Loop
                    } // End Project Loop
                } // End ActionPlan Loop
            } // End Strategic Loop

            /** =========================
             * อัปเดตสถานะ status_add_data ของปีนี้
             * ========================= */
            $year->status_add_data = 1;
            $year->save();

            return true;
        });
    }


    public function getYearAllNewokr($yearclient)
    {
        $id_year = Year::whereNull('deleted_at')
            ->where('year_id', '<', $yearclient)
            ->orderByDesc('year_id')
            ->value('year_id');


        // return $id_year;
        if (!$id_year) {
            return collect(); // กัน error กรณีมีปีเดียว
        }
        $dataOld =  Okr::where('id_year', $id_year)->with('OkrUsers')->orderBy('okr_number')->get();
        $dataOkr = Okr::where('id_year', $yearclient)->pluck('fk_okr')->toArray();

        return [
            'dataOld' => $dataOld,
            'dataOkr' => $dataOkr,
            'id_year' => $id_year
        ];
    }

    public function insertDatanewYearOkr($data, $newYear)
    {

        DB::transaction(function () use ($data, $newYear) {

            $strategicMap  = [];

            $year = Year::findOrFail($newYear);
            $yearValue = (int) $year->year;
            $timeStart = Carbon::create($yearValue, 10, 1)->startOfDay();
            $timeEnd   = Carbon::create($yearValue + 1, 9, 30)->endOfDay();

            /** =========================
             * Okr
             * ========================= */
            $index_s = 1;
            foreach ($data as $sData) {

                $s = Okr::findOrFail($sData);

                $newO = $s->replicate();
                $newO->id_year = $newYear;
                $newO->fk_okr = $s->okr_id;
                $newO->start_date = $timeStart;
                $newO->end_date = $timeEnd;
                $newO->save();

                $strategicMap[$s->okr_id] = $newO->okr_id;

                foreach ($s->OkrUsers as $okrUser) {
                    $newOu = $okrUser->replicate();
                    $newOu->id_okr = $newO->okr_id;
                    $newOu->save();
                }
            }
        });
    }
    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $year = Year::where("year_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $year->update([
            'status' => $year->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $year;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $year = Year::where('year_id', $id)->firstOrFail();
        $year->delete();
        return $year;
    }
    public function store(YearDTO $yearDTO)
    {
        $yearDB = new Year();

        DB::transaction(function () use ($yearDTO, $yearDB) {
            // Project
            $yearDB->year = $yearDTO->nameYear;
            $yearDB->budget = $yearDTO->budget;
            $yearDB->save();
        });

        return  $yearDB;
    }
    public function update(YearDTO $yearDTO, $id)
    {
        return DB::transaction(function () use ($yearDTO, $id) {
            $year = Year::where('year_id', $id)->firstOrFail();

            $year->year = $yearDTO->nameYear;

            $year->budget = $yearDTO->budget;

            $year->save();

            return $year;
        });
    }
}
