<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionPlan extends Model
{
    use SoftDeletes;

    protected $table = 'Action_Plan';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'action_plan_id';

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
        'action_plan_id',
        'action_plan_number',
        'name_ap',
        'budget',
        'spend_money',
        'status',
        'id_strategic',
    ];
    public function strategic()
    {
        return $this->belongsTo(Strategic::class, 'id_strategic'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
}
