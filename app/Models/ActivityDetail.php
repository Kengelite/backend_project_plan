<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ActivityDetail extends Model
{
    //
    use SoftDeletes;
    protected $table = 'Activity_detail';
    protected $primaryKey = 'activity_detail_id';
    protected $keyType = 'string';

}
