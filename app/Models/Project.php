<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use SoftDeletes;

    protected $table = 'Project';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'project_id';

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

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class, 'id_project');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }

    public function Objective()
    {
        return $this->hasMany(Objective::class, 'id_project');
    }
    public function projectStyle()
    {
        return $this->hasMany(StyleDetail::class, 'id_project');
    }
    public function projectPrinciple()
    {
        return $this->hasMany(projectPrinciple::class, 'id_project');
    }
    public function year()
    {
        return $this->belongsTo(year::class, 'id_year'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function projectIndicator()
    {
        return $this->hasMany(indicator::class, 'id_project');
    }


    protected $fillable = [
        'project_id',
        'project_number',
        'project_name',
        'budget',
        'spend_money',
        // 'agency',
        'status',
        'status_performance',
        'status_report',
        'detail_short',
        'abstract',
        'time_start',
        'time_end',
        'location',
        // 'OKR_id',
        // 'id_action_plan',
        // 'project_detail_id',

        'id_department',
        'result',
        'id_year',
        'obstacle',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
