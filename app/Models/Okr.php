<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Okr extends Model
{
    //
    use SoftDeletes;
    protected $table = 'okr';
    protected $primaryKey = 'okr_id';
    protected $keyType = 'string';
    public $incrementing = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit'); // โดยที่ unit_id คือ key ที่เชื่อมกัน
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'id_year'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function OkrUsers()
    {
        return $this->hasMany(OkrUser::class, 'id_okr');
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
}
