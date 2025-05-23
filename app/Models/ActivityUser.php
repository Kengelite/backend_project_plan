<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityUser extends Model
{
    protected $table = 'Activity_user';
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'id_activity'); // แก้ไขเป็น 'activity_id'
    }
}
