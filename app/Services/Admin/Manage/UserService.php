<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Dto\UserDTO;
use App\Models\User;
use App\Models\Okr;
use App\Models\ProjectUser;
use App\Trait\Utils;
use App\Models\ActivityUser;
use App\Models\OkrUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    use Utils;
    public function getAll($perPage)
    {
        $user = User::with('position')->paginate($perPage)->withQueryString();
        return $user;
    }

    public function getDataUserSumProjectActivty($id_user, $id_year)
    {
        /** =========================
         * Project
         * ========================= */
        $userProject = ProjectUser::join('project', 'project.project_id', '=', 'project_user.id_project')
            ->where('project_user.id_user', $id_user)
            ->where('project_user.id_year', $id_year)
            ->count();

        $userProject_report = ProjectUser::join('project', 'project.project_id', '=', 'project_user.id_project')
            ->where('project_user.id_user', $id_user)
            ->where('project_user.id_year', $id_year)
            ->where('project.status_report', 1)
            ->count();

        /** =========================
         * Activity
         * ========================= */
        $userActivity = ActivityUser::join('activity', 'activity.activity_id', '=', 'activity_user.id_activity')
            ->where('activity_user.id_user', $id_user)
            ->where('activity_user.id_year', $id_year)
            ->distinct('activity.activity_id')
            ->count();

        $userActivity_report = ActivityUser::join('activity', 'activity.activity_id', '=', 'activity_user.id_activity')
            ->where('activity_user.id_user', $id_user)
            ->where('activity_user.id_year', $id_year)
            ->where('activity.status_report', 1)
            ->distinct('activity.activity_id')
            ->count('activity.activity_id');

        /** =========================
         * OKR
         * ========================= */
        $userOkr = OkrUser::join('okr', 'okr.okr_id', '=', 'okr_user.id_okr')
            ->where('okr_user.id_user', $id_user)
            ->where('okr_user.id_year', $id_year)
            ->count();

        $userOkr_report = OkrUser::join('okr', 'okr.okr_id', '=', 'okr_user.id_okr')
            ->where('okr_user.id_user', $id_user)
            ->where('okr_user.id_year', $id_year)
            ->where('okr.status_report', 1)
            ->count();

        return [
            'userProject' => $userProject,
            'userActivity' => $userActivity,
            'userOkr' => $userOkr,

            'userProject_report' => $userProject_report,
            'userActivity_report' => $userActivity_report,
            'userOkr_report' => $userOkr_report,
        ];
    }

    public function getByIDUserProject($userId, $id_year, $perPage)
    {
        // $userId = Auth::id();

        $projects = ProjectUser::where('id_user', $userId)
            ->where('id_year', $id_year)
            ->whereHas('project', function ($query) {
                $query->where('status', 1);
            })
            ->with('project')
            ->paginate($perPage);

        // แก้: ใช้ getCollection() + ใช้ id_project ให้ถูก
        $projects->getCollection()->transform(function ($project) {

            // ใช้ id_project จาก ProjectUser (ค่าตรงกับ project.project_id)
            $projectId = $project->id_project;

            // ชื่อตารางลองเช็คอีกที ถ้าตารางจริงคือ activities ให้เปลี่ยนเป็น 'activities'
            $activitiesQuery = DB::table('Activity')
                ->where('id_project', $projectId)
                ->whereNull('deleted_at');

            $project->count_activity = (clone $activitiesQuery)->count();

            $project->count_activity_report = (clone $activitiesQuery)
                ->where('status_report', 1)
                ->count();

            return $project;
        });
        return $projects;
    }

    public function getByIDUserActivity($userId, $id_year, $perPage)
    {
        // $userId = Auth::id();

        $activityUser = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id_year)
            ->whereHas('Activity', function ($query) {
                $query->where('status', 1);
            })
            ->with('activity')
            ->paginate($perPage);

        return $activityUser;
    }


    public function getByIDUserOkr($userId, $id_year, $perPage)
    {
        // $userId = Auth::id();

        $activityUser = OkrUser::where('id_user', $userId)
            ->where('id_year', $id_year)
            ->whereHas('okr', function ($query) {
                $query->where('status', 1);
            })
            ->with('okr')
            ->paginate(10);

        return $activityUser;
    }


    public function store(UserDTO $userDTO)
    {

        $user = new User();

        if ($userDTO->urlImg) {
            $resultFile = $this->storeFile($userDTO->urlImg, '/uploads/user');
            $user->url_img        = @$resultFile['file_name'];
        }

        $user->name = $userDTO->name;
        $user->email = $userDTO->email;
        $user->password = bcrypt($userDTO->password);
        $user->role = $userDTO->academicPosition;
        $user->academic_position = $userDTO->academicPosition;
        $user->id_position = $userDTO->idPosition;
        $user->save();
        return $user;
    }


    public function update($id, UserDTO $userDTO)
    {

        $user =  User::findOrFail($id);

        if ($userDTO->urlImg) {
            $oldImage              = '/uploads/user/' . $user->url_img;
            $result                = @$this->deleteFile($oldImage);
            $resultFile = $this->storeFile($userDTO->urlImg, '/uploads/user');
            $user->url_img        = @$resultFile['file_name'];
        }

        $user->name = $userDTO->name;
        $user->email = $userDTO->email;
        if (!empty($userDTO->password)) {
            $user->password = bcrypt($userDTO->password);
        }
        $user->role = $userDTO->academicPosition;
        $user->academic_position = $userDTO->academicPosition;
        $user->id_position = $userDTO->idPosition;
        $user->save();
        return $user;
    }



    public function getByID($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function getUserTeacher()
    {
        $user = User::select('id', 'name', 'email', 'academic_position', 'id_position') // ระบุเฉพาะ field ที่ต้องการ
            ->where('academic_position', '1')
            ->with('position') // ดึง relation position ด้วย
            ->orderBy('id')
            ->get();

        return $user;
    }
    public function getUserEmployee()
    {
        $user = User::select('id', 'name', 'email', 'academic_position', 'id_position') // ระบุเฉพาะ field ที่ต้องการ
            ->where('academic_position', '2')
            ->with('position')
            ->orderBy('id')
            ->get();
        return $user;
    }

    public function updateStatus($id)
    {
        // ดึงข้อมูลที่ต้องการอัปเดตจากฐานข้อมูล
        $user = User::where("activity_id", $id);

        // สลับสถานะจาก 0 เป็น 1 หรือจาก 1 เป็น 0
        $updated = $user->update([
            'status' => $user->first()->status == 0 ? 1 : 0
        ]);


        // คืนค่าข้อมูลที่ถูกอัปเดต
        return $user;
    }

    public function getByIDUser($id)
    {
        $userId = Auth::id();

        $userUser = ActivityUser::where('id_user', $userId)
            ->where('id_year', $id)
            ->whereHas('activity', function ($query) {
                $query->where('status', 1);
            })
            ->with('activity')
            ->paginate(10);

        return $userUser;
    }

    public function getByIDYear($id, $perPage)
    {

        $project = User::where('id_year', $id)
            ->orderBy('activity_id')
            // ->with('strategic')
            ->paginate($perPage);

        return $project;
    }
    public function delete($id)
    {
        // $strategic = Strategic::findOrFail($id)->delete();
        // return $strategic;

        $user = User::where('id', $id)->firstOrFail();

        if ($user->url_img) {
            $oldImage              = '/uploads/user/' . $user->url_img;
            $result                = @$this->deleteFile($oldImage);
        }

        $user->delete();
        return $user;
    }
}
