<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    protected $fillable = ['day', 'start_time', 'end_time', 'is_active'];

    public function availabilities()
    {
        return $this->hasMany(FacultyAvailability::class);
    }
}
