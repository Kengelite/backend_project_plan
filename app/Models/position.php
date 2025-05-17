<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class position extends Model
{
    //
    use SoftDeletes;
    protected $table = 'position';
    protected $primaryKey = 'position_id';
    protected $keyType = 'string';
    public function users()
    {
        return $this->hasMany(User::class, 'id_position');
    }
}
