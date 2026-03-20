<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name', 'semester', 'level', 'division_count', 'division_names'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function getDivisionListAttribute(): array
    {
        $count = (int) ($this->division_count ?: 1);
        $count = max(1, min(10, $count));

        if (!empty($this->division_names)) {
            $names = array_values(array_filter(array_map('trim', explode(',', $this->division_names))));
            if (!empty($names)) {
                return $names;
            }
        }

        $letters = [];
        for ($i = 0; $i < $count; $i++) {
            $letters[] = chr(ord('A') + $i);
        }
        return $letters;
    }
}
