<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Classroom;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => Teacher::count(),
            'subjects' => Subject::count(),
            'courses' => Course::count(),
            'classrooms' => Classroom::count(),
            'labs' => Classroom::where('room_name', 'like', '%lab%')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
