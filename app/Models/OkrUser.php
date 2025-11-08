<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class OkrUser extends Model
{
    use SoftDeletes;
    //
    protected $table = 'okr_user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'okr_user_id';

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

    public function okr()
    {
        return $this->belongsTo(Okr::class, 'id_okr'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }

    // app/Models/ProjectUser.php
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    protected $fillable = [
        'okr_user_id',
        'type',
        'main',
        'status',
        'id_user',
        'id_okr',
        'id_year',

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
}
