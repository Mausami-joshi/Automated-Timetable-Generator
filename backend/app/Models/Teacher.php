<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name', 'email', 'department', 'color_code'];

    public function availabilities()
    {
        return $this->hasMany(FacultyAvailability::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
