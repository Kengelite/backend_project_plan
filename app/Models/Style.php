<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Style extends Model
{
    use SoftDeletes;
    protected $table = 'style';
    protected $primaryKey = 'style_id';
    protected $keyType = 'int';

}
