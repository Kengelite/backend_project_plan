<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Strategic extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'strategic_id';
    protected $table = 'Strategic';
    public $incrementing = false;       // ต้องใส่!
    protected $keyType = 'string';      // UUID เป็น string
    public function actionplan()
    {
        return $this->hasMany(actionplan::class, 'id_strategic');
    }

    public function actionPlans()
    {
        return $this->hasMany(ActionPlan::class, 'id_strategic', 'strategic_id');
    }

    public function activities()
    {
        return $this->hasManyThrough(
            Activity::class,
            ActionPlan::class,
            'id_strategic',     // FK ใน action_plans
            'id_project',       // FK ใน activities
            'strategic_id',     // local key
            'action_plan_id'    // local key ใน action_plans
        );
    }


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
        'strategic_id',
        'strategic_number',
        'strategic_name',
        'budget',
        'id_year'
    ];
}
