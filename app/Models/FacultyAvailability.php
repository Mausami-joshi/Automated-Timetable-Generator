<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyAvailability extends Model
{
    protected $fillable = ['teacher_id', 'timeslot_id', 'is_available'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }
}
