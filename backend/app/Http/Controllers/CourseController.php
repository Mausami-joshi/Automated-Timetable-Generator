<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::all();
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:10',
            'level' => 'required|in:UG,PG',
            'division_count' => 'required|integer|min:1|max:10',
            'division_names' => 'nullable|string|max:255',
        ]);

        Course::create($validated);
        return redirect()->route('courses.index')->with('success', 'Course added successfully!');
    }

    public function show(Course $course)
    {
        // Optional
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:10',
            'level' => 'required|in:UG,PG',
            'division_count' => 'required|integer|min:1|max:10',
            'division_names' => 'nullable|string|max:255',
        ]);

        $course->update($validated);
        return redirect()->route('courses.index')->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Course deleted successfully!');
    }
}
