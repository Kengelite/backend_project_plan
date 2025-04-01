<?php

namespace App\Services\Admin\Manage;

use App\Dto\ActivityDTO;
use App\Models\Activity;
use App\Trait\Utils;


class ActivityService
{
    use Utils;
    public function getAll()
    {
        $activity = Activity::paginate(10)->withQueryString();
        return $activity;
    }
    public function getByID($id)
    {
        $activity = Activity::findOrFail($id);
        return $activity;
    }
}
