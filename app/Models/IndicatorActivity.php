<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class IndicatorActivity extends Model
{
    use SoftDeletes;

    protected $table = 'indicator_activity';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'indicator_activity_id';

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

    protected $fillable = [
        'indicator_activity_id',
        'indicator_name',
        'id_unit',
        'goal',
        'created_at',
        'updated_at',
        'deleted_at',
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
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
}
