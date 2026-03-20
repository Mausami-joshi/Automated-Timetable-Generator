<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Classroom;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::all();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('admin.classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1'
        ]);

        Classroom::create($validated);
        return redirect()->route('classrooms.index')->with('success', 'Classroom added successfully!');
    }

    public function show(Classroom $classroom)
    {
        // Optional
    }

    public function edit(Classroom $classroom)
    {
        return view('admin.classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1'
        ]);

        $classroom->update($validated);
        return redirect()->route('classrooms.index')->with('success', 'Classroom updated successfully!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return redirect()->route('classrooms.index')->with('success', 'Classroom deleted successfully!');
    }
}
