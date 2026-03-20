<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FacultyAvailability;
use App\Models\Teacher;
use App\Models\Timeslot;

class FacultyAvailabilityController extends Controller
{
    public function index()
    {
        $availabilities = FacultyAvailability::with(['teacher', 'timeslot'])->get();
        return view('admin.faculty-availabilities.index', compact('availabilities'));
    }

    public function create()
    {
        $teachers = Teacher::all();
        $timeslots = Timeslot::orderBy('day')->orderBy('start_time')->get();
        return view('admin.faculty-availabilities.create', compact('teachers', 'timeslots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'timeslot_id' => 'required|exists:timeslots,id',
            'is_available' => 'boolean'
        ]);

        $validated['is_available'] = $request->has('is_available') ? true : false;

        FacultyAvailability::create($validated);
        return redirect()->route('faculty-availabilities.index')->with('success', 'Availability added successfully!');
    }

    public function show(FacultyAvailability $facultyAvailability)
    {
        // Optional
    }

    public function edit(FacultyAvailability $facultyAvailability)
    {
        $teachers = Teacher::all();
        $timeslots = Timeslot::orderBy('day')->orderBy('start_time')->get();
        return view('admin.faculty-availabilities.edit', compact('facultyAvailability', 'teachers', 'timeslots'));
    }

    public function update(Request $request, FacultyAvailability $facultyAvailability)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'timeslot_id' => 'required|exists:timeslots,id',
            'is_available' => 'boolean'
        ]);

        $validated['is_available'] = $request->has('is_available') ? true : false;

        $facultyAvailability->update($validated);
        return redirect()->route('faculty-availabilities.index')->with('success', 'Availability updated successfully!');
    }

    public function destroy(FacultyAvailability $facultyAvailability)
    {
        $facultyAvailability->delete();
        return redirect()->route('faculty-availabilities.index')->with('success', 'Availability deleted successfully!');
    }
}
