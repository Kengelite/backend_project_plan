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
    protected $primaryKey = 'data_id';
    // protected $keyType = 'string';
    public $incrementing = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';


    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity'); // โดยที่ unit_id คือ key ที่เชื่อมกัน
    }

    protected $fillable = [
        'id_file',
        'id_activity',
        'money',
    ];
}
