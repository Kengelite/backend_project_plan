<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityPrinciple extends Model
{
    use SoftDeletes;

    protected $table = 'Activity_principle';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'activity_principle_id';

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
        'activity_principle_id',
        'id_activity',
        'id_principle',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
