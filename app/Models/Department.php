<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Department extends Model
{
    use SoftDeletes;
    protected $table = 'departments';
    protected $primaryKey = 'departments_id';
    protected $keyType = 'string';

}
