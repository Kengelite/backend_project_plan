<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class indicator extends Model
{
 use SoftDeletes;
    protected $table = 'indicator';
    protected $primaryKey = 'indicator_id';
    protected $keyType = 'string';


    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    // public function ActivityUsers()
    // {
    //     return $this->hasMany(ActivityUser::class, 'activity_id');
    // }

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
        'indicator_id',
        'indicator_name',
        'goal',
        'id_project',
        'id_unit',

        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
