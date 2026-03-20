@extends('layouts.admin')

@section('title', 'Edit Timeslot')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Edit Timeslot: {{ $timeslot->day }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('timeslots.update', $timeslot) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Day of Week</label>
                <select name="day" class="form-select @error('day') is-invalid @enderror" required>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        <option value="{{ $day }}" {{ old('day', $timeslot->day) == $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
                @error('day') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Start Time</label>
                    <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', \Carbon\Carbon::parse($timeslot->start_time)->format('H:i')) }}" required>
                    @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">End Time</label>
                    <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', \Carbon\Carbon::parse($timeslot->end_time)->format('H:i')) }}" required>
                    @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-4 form-check">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $timeslot->is_active) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="is_active">Active for scheduling</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('timeslots.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Timeslot</button>
            </div>
        </form>
    </div>
</div>
@endsection
