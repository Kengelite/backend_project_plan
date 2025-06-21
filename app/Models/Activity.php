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
    public function ActivityUsers()
    {
        return $this->hasMany(ActivityUser::class, 'activity_id');
    }

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
}
