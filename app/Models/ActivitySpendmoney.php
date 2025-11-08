<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivitySpendmoney extends Model
{
    use SoftDeletes;
    protected $table = 'Activity_spendmoney';
    protected $primaryKey = 'activity_spendmoney_id';
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

    public function ActivityDetailSpendmoney()
    {
        return $this->hasMany(ActivityDetailSpendmoney::class, 'id_activity_spendmoney');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit'); // โดยที่ unit_id คือ key ที่เชื่อมกัน
    }


    protected $fillable = [
        'activity_spendmoney_name',
        'id_unit',
    ];

    // public function spendmoneyDetails()
    // {
    //     return $this->hasMany(ActivityDetailSpendmoney::class, 'id_activity_spendmoney');
    // }

    protected static function booted()
    {
        static::deleting(function ($model) {
            // Soft Delete ข้อมูลลูกให้ด้วย
            $model->ActivityDetailSpendmoney()->delete();
        });
    }
}
