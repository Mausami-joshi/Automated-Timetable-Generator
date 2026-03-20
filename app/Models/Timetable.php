<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = ['course_id', 'division', 'subject_id', 'teacher_id', 'room_id', 'timeslot_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room()
    {
        return $this->belongsTo(Classroom::class, 'room_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }
}
