<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivityUser extends Model
{
    protected $table = 'Activity_user';
    use SoftDeletes;
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_activity_user';
    public $incrementing = true;
    protected $keyType = 'int';
    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */


    /**
     * The data type of the primary key ID.
     *
     * @var string
     */


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity'); // แก้ไขเป็น 'activity_id'
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
