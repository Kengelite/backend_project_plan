<?php

namespace App\Services\Admin\Manage;  // คำสั่ง namespace ต้องอยู่บรรทัดแรก

use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Models\Project;
use App\Models\Year;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityUserService
{
    use Utils;

    public function store($id_user, $id_activity, $type, $id_year)
    {
        $projectDB = new ActivityUser();

        DB::transaction(function () use ($id_user, $id_activity, $type, $id_year, &$projectDB) {
            $projectDB->id_user = $id_user;
            $projectDB->id_activity = $id_activity;
            $projectDB->type = $type;
            $projectDB->id_year = $id_year;
            $projectDB->main = 0;
            $projectDB->save();
        });

        // ส่งเฉพาะ id กลับ
        return $projectDB;
    }

    public function update($id, $id_user)
    {

        $projectDB = ActivityUser::where('id_activity_user', $id)->firstOrFail();
        $projectDB->id_user = $id_user;
        $projectDB->save();
        return $projectDB;
    }
    public function delete($id,$type)
    {
        $project = ActivityUser::where('id_activity_user', $id)->firstOrFail();
        $isMain = $project->main == 1;
        $idProject = $project->id_activity;

        $data =  DB::transaction(function () use ($project, $isMain, $idProject,$type) {
            $project->delete();

            if ($isMain) {
                $nextMainUser = ActivityUser::where('id_activity', $idProject)
                    ->where('type',$type)
                    ->orderBy('created_at')
                    ->first();

                if ($nextMainUser) {
                    $nextMainUser->main = 1;
                    $nextMainUser->save();
                }
                // return $nextMainUser;
            }
            // return $isMain;
        });
        return $project;
    }
}
