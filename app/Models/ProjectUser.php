<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUser extends Model
{
    use SoftDeletes;
    //
    protected $table = 'Project_user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_project_user';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }

    // app/Models/ProjectUser.php
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    protected $fillable = [
        'id_project_user',
        'type',
        'main',
        'status',
        'id_user',
        'id_project',
        'id_year',

        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
