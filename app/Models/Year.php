<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Year extends Model
{
    //
    use SoftDeletes;
    protected $table = 'Year';
    protected $primaryKey = 'year_id';
    protected $keyType = 'int';
}
