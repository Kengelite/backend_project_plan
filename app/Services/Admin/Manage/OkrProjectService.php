<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\OkrDetailProject;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class OkrProjectService
{
    use Utils;



    public function store($id_okr, $id_project)
    {
        $projectDB = new OkrDetailProject();

        DB::transaction(function () use ($id_okr, $id_project, &$projectDB) {
            $projectDB->id_okr = $id_okr;
            $projectDB->id_project = $id_project;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $id_okr)
    {

        $projectDB = OkrDetailProject::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $projectDB->id_okr = $id_okr;
        $projectDB->save();
        return $projectDB;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = OkrDetailProject::where('okr_detail_project_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
