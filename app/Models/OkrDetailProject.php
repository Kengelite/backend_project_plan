<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OkrDetailProject extends Model
{
    use SoftDeletes;

    protected $table = 'okr_detail_project';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'okr_detail_project_id';

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
        'okr_detail_project_id',
        'id_project',
        'id_okr',
    ];
}
