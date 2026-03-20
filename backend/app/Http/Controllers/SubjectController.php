<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Subject;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::with('course')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $courses = Course::all();
        $teachers = Teacher::orderBy('department')->orderBy('name')->get();
        return view('admin.subjects.create', compact('courses', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'hours_per_week' => 'required|integer|min:1|max:40',
            'term' => 'required|in:odd,even',
            'is_lab' => 'sometimes|boolean',
        ]);

        $validated['is_lab'] = $request->boolean('is_lab');
        if ($validated['is_lab'] && ($validated['hours_per_week'] % 2 !== 0)) {
            throw ValidationException::withMessages(['hours_per_week' => 'Lab subjects must have even hours per week (2,4,6...).']);
        }

        $course = Course::find($validated['course_id']);
        if ($course) {
            $isOddSemester = in_array($course->semester, [1, 3, 5, 7], true);
            if ($validated['term'] === 'odd' && !$isOddSemester) {
                throw ValidationException::withMessages(['term' => 'Odd term subjects must be assigned to an odd semester course (1,3,5,7).']);
            }
            if ($validated['term'] === 'even' && $isOddSemester) {
                throw ValidationException::withMessages(['term' => 'Even term subjects must be assigned to an even semester course (2,4,6,8).']);
            }
        }

        Subject::create($validated);
        return redirect()->route('subjects.index')->with('success', 'Subject added successfully!');
    }

    public function show(Subject $subject)
    {
        // Optional
    }

    public function edit(Subject $subject)
    {
        $courses = Course::all();
        $teachers = Teacher::orderBy('department')->orderBy('name')->get();
        return view('admin.subjects.edit', compact('subject', 'courses', 'teachers'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'hours_per_week' => 'required|integer|min:1|max:40',
            'term' => 'required|in:odd,even',
            'is_lab' => 'sometimes|boolean',
        ]);

        $validated['is_lab'] = $request->boolean('is_lab');
        if ($validated['is_lab'] && ($validated['hours_per_week'] % 2 !== 0)) {
            throw ValidationException::withMessages(['hours_per_week' => 'Lab subjects must have even hours per week (2,4,6...).']);
        }

        $course = Course::find($validated['course_id']);
        if ($course) {
            $isOddSemester = in_array($course->semester, [1, 3, 5, 7], true);
            if ($validated['term'] === 'odd' && !$isOddSemester) {
                throw ValidationException::withMessages(['term' => 'Odd term subjects must be assigned to an odd semester course (1,3,5,7).']);
            }
            if ($validated['term'] === 'even' && $isOddSemester) {
                throw ValidationException::withMessages(['term' => 'Even term subjects must be assigned to an even semester course (2,4,6,8).']);
            }
        }

        $subject->update($validated);
        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully!');
    }
}
