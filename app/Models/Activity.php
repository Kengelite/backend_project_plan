<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;
    protected $table = 'Activity';
    protected $primaryKey = 'activity_id';
    protected $keyType = 'string';
    public $incrementing = false;


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $fillable = [
        'activity_id',
        'id',
        'name_activity',
        'budget',
        'spend_money',
        'agency',
        'status',
        'status_performance',
        'status_report',
        // 'detail_short',
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'activty_user', 'id_activity', 'id_user')
            ->withPivot(['type', 'main', 'status', 'id_year'])
            ->withTimestamps();
    }
    public function ActivityUsers()
    {
        return $this->hasMany(ActivityUser::class, 'id_activity');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function ObjectiveActivity()
    {
        return $this->hasMany(ObjectiveActivity::class, 'id_activity');
    }
    public function activityOkr()
    {
        return $this->hasMany(OkrDetailActivity::class, 'id_activity');
    }
    public function activityPrinciple()
    {
        return $this->hasMany(ActivityPrinciple::class, 'id_activity');
    }
    public function activityStyle()
    {
        return $this->hasMany(StyleActivtiyDetail::class, 'id_activity');
    }
    public function activityspendmoney()
    {
        return $this->hasMany(ActivitySpendmoney::class, 'id_activity');
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'id_year'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function activityIndicator()
    {
        return $this->hasMany(IndicatorActivity::class, 'id_activity');
    }
}
