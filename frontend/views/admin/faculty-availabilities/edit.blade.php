@extends('layouts.admin')

@section('title', 'Edit Faculty Availability')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Edit Availability Record</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('faculty-availabilities.update', $facultyAvailability) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Select Teacher</label>
                <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $facultyAvailability->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }} ({{ $teacher->department }})</option>
                    @endforeach
                </select>
                @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Select Timeslot</label>
                <select name="timeslot_id" class="form-select @error('timeslot_id') is-invalid @enderror" required>
                    @foreach($timeslots as $timeslot)
                        <option value="{{ $timeslot->id }}" {{ old('timeslot_id', $facultyAvailability->timeslot_id) == $timeslot->id ? 'selected' : '' }}>
                            {{ $timeslot->day }} | {{ \Carbon\Carbon::parse($timeslot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($timeslot->end_time)->format('h:i A') }}
                        </option>
                    @endforeach
                </select>
                @error('timeslot_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch form-switch-lg">
                    <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1" {{ old('is_available', $facultyAvailability->is_available) ? 'checked' : '' }}>
                    <label class="form-check-label ms-2 fw-bold" for="is_available">Available for classes</label>
                </div>
                <small class="text-muted">Turn off if the teacher is specifically requested to be free during this slot.</small>
                @error('is_available') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('faculty-availabilities.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Availability</button>
            </div>
        </form>
    </div>
</div>
@endsection
