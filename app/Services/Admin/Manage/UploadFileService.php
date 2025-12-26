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

class UploadFileService
{
    use Utils;



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



}
