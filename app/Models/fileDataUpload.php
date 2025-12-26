<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class fileDataUpload extends Model
{
    //
    use SoftDeletes;
    protected $table = 'file_data_upload';
    protected $primaryKey = 'id_data';
    // protected $keyType = 'string';
    public $incrementing = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';
   
}
