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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'id_year'); // โดยที่ project_id คือ key ที่เชื่อมกัน
    }
}
