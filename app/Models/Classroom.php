<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['room_name', 'capacity'];

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'room_id');
    }
}
