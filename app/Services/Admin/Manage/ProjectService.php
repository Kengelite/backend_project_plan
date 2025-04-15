<?php

namespace App\Services\Admin\Manage;

use App\Dto\ProjectDTO;
use App\Models\Project;
use App\Trait\Utils;


class ProjectService
{
    use Utils;
    public function getAll()
    {
        $project = Project::paginate(10)->withQueryString();
        return $project;
    }
    public function getByID($id)
    {
        $project = Project::findOrFail($id);
        return $project;
    }
    public function getByIDactionplan($id)
    {
        $project = Project::where('id_action_plan',$id)
        ->orderBy('project_number')
        ->paginate(10)
        ->withQueryString();
        return $project;
    }

}
