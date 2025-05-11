<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    //
    protected $table = 'Project_user';
    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
}
