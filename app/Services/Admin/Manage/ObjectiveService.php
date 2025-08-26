<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\Objective;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class ObjectiveService
{
    use Utils;



    public function store($name, $id_project)
    {
        $projectDB = new Objective();

        DB::transaction(function () use ($name, $id_project, &$projectDB) {
            $projectDB->objective_name = $name;
            $projectDB->id_project = $id_project;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $name)
    {

        $projectDB = Objective::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $projectDB->objective_name = $name;
        $projectDB->save();


        return $projectDB;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = Objective::where('objective_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
