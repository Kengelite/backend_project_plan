<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class roundFileUpload extends Model
{
    use SoftDeletes;
    protected $primaryKey = 'file_id';
    protected $table = 'round_file_upload';
    public $incrementing = false;       // ต้องใส่!
    protected $keyType = 'string';      // UUID เป็น string


    public function filedataupload()
    {
        return $this->hasMany(fileDataUpload::class, 'id_file');
    }


    public function year()
    {
        return $this->belongsTo(year::class, 'id_year');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    protected $fillable = [
        'file_name',
        'round',
        'id_year',
        'id_user',
    ];
}
