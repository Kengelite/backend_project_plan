<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Okr extends Model
{
    //
    use SoftDeletes;
    protected $table = 'okr';
    protected $primaryKey = 'okr_id';
    protected $keyType = 'string';
}
