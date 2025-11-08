<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivityDetail extends Model
{
    //
    use SoftDeletes;
    protected $table = 'Activity_detail';
    protected $primaryKey = 'activity_detail_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public function ActivityDetailSpendmoney()
    {
        return $this->hasMany(ActivityDetailSpendmoney::class, 'id_activity_detail');
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

    public function ActivitySpendmoney()
    {
        return $this->belongsTo(
            ActivitySpendmoney::class,
            'id_activity_spendmoney',    // FK ที่อยู่ในตารางนี้
            'activity_spendmoney_id'     // PK ที่อยู่ในตาราง activity_spendmoney
        );
    }
    public function spendmoneyDetails()
    {
        return $this->hasMany(ActivityDetailSpendmoney::class, 'id_activity_detail');
    }

    protected static function booted()
    {
        static::deleting(function ($model) {
            // Soft Delete ข้อมูลลูกให้ด้วย
            $model->spendmoneyDetails()->delete();
        });
    }
}
