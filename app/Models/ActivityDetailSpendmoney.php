<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivityDetailSpendmoney extends Model
{
    use SoftDeletes;
    protected $table = 'activity_detail_spendmoney';
    protected $primaryKey = 'activity_detail_spendmoney_id';
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (empty($model->{$model->getKeyName()})) {
    //             $model->{$model->getKeyName()} = (string) Str::uuid();
    //         }
    //     });
    // }




    public function ActivitySpendmoney()
    {
        return $this->belongsTo(
            ActivitySpendmoney::class,
            'id_activity_spendmoney',    // FK ที่อยู่ในตารางนี้
            'activity_spendmoney_id'     // PK ที่อยู่ในตาราง activity_spendmoney
        );
    }

    protected $fillable = [
        'id_activity_detail',
        'id_activity_spendmoney',
        'price',
        'amount',
    ];
}
