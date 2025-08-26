<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Dto\UserDTO;
use App\Models\User;
use App\Trait\Utils;
use App\Models\ActivityUser;
use Illuminate\Support\Facades\Auth;

class UserService
{
    use Utils;
    public function getAll($perPage)
    {
        $user = User::with('position')->paginate($perPage)->withQueryString();
        return $user;
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
