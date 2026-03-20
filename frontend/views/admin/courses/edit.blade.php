@extends('layouts.admin')

@section('title', 'Edit Course')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Edit Course: {{ $course->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('courses.update', $course) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Course Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $course->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Level</label>
                <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                    <option value="UG" {{ old('level', $course->level ?? 'UG') == 'UG' ? 'selected' : '' }}>UG</option>
                    <option value="PG" {{ old('level', $course->level) == 'PG' ? 'selected' : '' }}>PG</option>
                </select>
                @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Semester</label>
                <input type="number" name="semester" class="form-control @error('semester') is-invalid @enderror" value="{{ old('semester', $course->semester) }}" min="1" max="10" required>
                @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Number of Divisions</label>
                <input type="number" name="division_count" class="form-control @error('division_count') is-invalid @enderror" value="{{ old('division_count', $course->division_count ?? 1) }}" min="1" max="10" required>
                @error('division_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Division Names (comma separated)</label>
                <input type="text" name="division_names" class="form-control @error('division_names') is-invalid @enderror" value="{{ old('division_names', $course->division_names) }}" placeholder="e.g. A,B,C">
                <div class="form-text">Leave empty to auto-generate (A, B, C…).</div>
                @error('division_names') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('courses.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
