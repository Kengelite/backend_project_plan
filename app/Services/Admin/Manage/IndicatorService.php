<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\indicator;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class IndicatorService
{
    use Utils;



    public function store($data, $id_project)
    {
        $indicatorDB = new Indicator();

        DB::transaction(function () use ($data, $id_project, &$indicatorDB) {
            $indicatorDB->indicator_name = $data->indicator_name;
            $indicatorDB->goal = $data->goal;
            $indicatorDB->id_project = $id_project;
            $indicatorDB->id_unit = $data->id_unit;
            $indicatorDB->save();
        });

        return $indicatorDB;
    }

    public function update($id, $indicator_name,$goal,$id_unit)
    {

        $indicatorDB = indicator::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
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

        $project = indicator::where('indicator_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
