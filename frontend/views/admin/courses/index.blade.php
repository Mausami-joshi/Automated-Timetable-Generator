@extends('layouts.admin')

@section('title', 'Courses Directory')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Courses List</h5>
        <a href="{{ route('courses.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add New</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Semester</th>
                        <th>Associated Subjects</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    <tr>
                        <td>{{ $course->id }}</td>
                        <td class="fw-bold">{{ $course->name }}</td>
                        <td><span class="badge bg-primary rounded-pill">Semester {{ $course->semester }}</span></td>
                        <td>
                            <span class="text-muted">{{ $course->subjects()->count() }} Subjects</span>
                        </td>
                        <td>
                            <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this course?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No courses found. Click "Add New" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
