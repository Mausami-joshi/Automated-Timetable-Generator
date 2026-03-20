@extends('layouts.admin')

@section('title', 'Subjects Directory')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Subjects List</h5>
        <a href="{{ route('subjects.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add New</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Subject Name</th>
                        <th>Course / Semester</th>
                        <th>Hours / Week</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>{{ $subject->id }}</td>
                        <td class="fw-bold">{{ $subject->subject_name }}</td>
                        <td>
                            @if($subject->course)
                            <span class="badge bg-info text-dark">{{ $subject->course->name }}</span>
                            <small class="text-muted ms-1">Sem {{ $subject->course->semester }}</small>
                            @else
                            <span class="text-muted fst-italic">Unassigned</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary">{{ $subject->hours_per_week }} hrs</span></td>
                        <td>
                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this subject?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No subjects found. Click "Add New" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
