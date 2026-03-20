@extends('layouts.admin')

@section('title', 'Add New Classroom')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Classroom Details</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('classrooms.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Room Name / Number</label>
                <input type="text" name="room_name" class="form-control @error('room_name') is-invalid @enderror" value="{{ old('room_name') }}" required placeholder="e.g. Lab 201">
                @error('room_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Capacity (Number of Seats)</label>
                <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', 60) }}" min="1" required>
                @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('classrooms.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Classroom</button>
            </div>
        </form>
    </div>
</div>
@endsection
