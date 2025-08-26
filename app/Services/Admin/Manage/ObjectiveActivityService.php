<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\ObjectiveActivity;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class ObjectiveActivityService
{
    use Utils;



    public function store($name, $id_activity)
    {
        $projectDB = new ObjectiveActivity();

        DB::transaction(function () use ($name, $id_activity, &$projectDB) {
            $projectDB->objective_activity_name = $name;
            $projectDB->id_activity = $id_activity;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $name)
    {

        $projectDB = ObjectiveActivity::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $projectDB->objective_activity_name = $name;
        $projectDB->save();


        return $projectDB;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = ObjectiveActivity::where('objective_activity_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
