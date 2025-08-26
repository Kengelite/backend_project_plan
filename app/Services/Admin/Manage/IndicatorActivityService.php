<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\IndicatorActivity;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class IndicatorActivityService
{
    use Utils;



    public function store($id_unit,$goal,$indicator_name, $id_activity)
    {
        $indicatorDB = new IndicatorActivity();

        DB::transaction(function () use ($id_unit, $goal,$indicator_name,$id_activity, &$indicatorDB) {
            $indicatorDB->indicator_name = $indicator_name;
            $indicatorDB->goal = $goal;
            $indicatorDB->id_activity = $id_activity;
            $indicatorDB->id_unit = $id_unit;
            $indicatorDB->save();
        });

        return $indicatorDB;
    }

    public function update($id, $indicator_name,$goal,$id_unit)
    {

        $indicatorDB = IndicatorActivity::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $indicatorDB->indicator_name = $indicator_name;
        $indicatorDB->goal = $goal;
        $indicatorDB->id_unit = $id_unit;
        $indicatorDB->save();
        return $indicatorDB;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = IndicatorActivity::where('indicator_activity_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
