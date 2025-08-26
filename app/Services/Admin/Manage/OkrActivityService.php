<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\ActionPlan;
use App\Models\OkrDetailActivity;
use App\Trait\Utils;
use Illuminate\Support\Facades\DB;

class OkrActivityService
{
    use Utils;



    public function store($id_okr, $id_activity)
    {
        $projectDB = new OkrDetailActivity();

        DB::transaction(function () use ($id_okr, $id_activity, &$projectDB) {
            $projectDB->id_okr = $id_okr;
            $projectDB->id_activity = $id_activity;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $id_okr)
    {

        $projectDB = OkrDetailActivity::findOrFail($id); // ค้นหาด้วย ID ที่รับเข้ามา
        $projectDB->id_okr = $id_okr;
        $projectDB->save();
        return $projectDB;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $project = OkrDetailActivity::where('okr_detail_activity_id', $id)->firstOrFail();
        $project->delete();
        return $project;
    }
}
