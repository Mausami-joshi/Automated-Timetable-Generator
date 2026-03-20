@extends('layouts.admin')

@section('title', 'Edit Teacher')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Edit Teacher: {{ $teacher->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teachers.update', $teacher) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $teacher->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $teacher->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Department</label>
                <input type="text" name="department" class="form-control @error('department') is-invalid @enderror" value="{{ old('department', $teacher->department) }}" required>
                @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Color Code (Optional)</label>
                <div class="d-flex align-items-center">
                    <input type="color" name="color_code" class="form-control form-control-color me-2" value="{{ old('color_code', $teacher->color_code) ?? '#2a5298' }}" title="Choose your color">
                    <small class="text-muted">Used for timetable visual representation</small>
                </div>
                @error('color_code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('teachers.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Teacher</button>
            </div>
        </form>
    </div>
</div>
@endsection
