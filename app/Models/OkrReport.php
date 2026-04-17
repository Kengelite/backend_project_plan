<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OkrReport extends Model
{
    use SoftDeletes;

    protected $table = 'okr_report';
    protected $primaryKey = 'report_okr_id';

    protected $fillable = [
        'id_okr',
        'id_user',
        'report_date',
        'result_value',
        'detail_link',
        'detail_report',
    ];
}