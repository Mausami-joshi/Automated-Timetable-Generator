<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Timeslot;

class TimeslotController extends Controller
{
    public function index()
    {
        $timeslots = Timeslot::orderBy('day')->orderBy('start_time')->get();
        return view('admin.timeslots.index', compact('timeslots'));
    }

    public function create()
    {
        return view('admin.timeslots.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Timeslot::create($validated);
        return redirect()->route('timeslots.index')->with('success', 'Timeslot added successfully!');
    }

    public function show(Timeslot $timeslot)
    {
        // Optional
    }

    public function edit(Timeslot $timeslot)
    {
        return view('admin.timeslots.edit', compact('timeslot'));
    }

    public function update(Request $request, Timeslot $timeslot)
    {
        $validated = $request->validate([
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'sometimes|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $timeslot->update($validated);
        return redirect()->route('timeslots.index')->with('success', 'Timeslot updated successfully!');
    }

    public function destroy(Timeslot $timeslot)
    {
        $timeslot->delete();
        return redirect()->route('timeslots.index')->with('success', 'Timeslot deleted successfully!');
    }
}
