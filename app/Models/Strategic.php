<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Strategic extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'strategic_id';
    protected $table = 'Strategic';
    public $incrementing = false;       // ต้องใส่!
    protected $keyType = 'string';      // UUID เป็น string
    public function actionplan()
    {
        return $this->hasMany(actionplan::class, 'strategic_id');
    }
}
