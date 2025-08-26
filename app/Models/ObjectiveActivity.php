<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ObjectiveActivity extends Model
{
    use SoftDeletes;

    protected $table = 'objective_activity';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'objective_activity_id';

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
        'objective_activity_id',
        'objective_activity_name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->objective_activity_id)) {
                $model->objective_activity_id = Str::uuid();
            }
        });
    }
}
