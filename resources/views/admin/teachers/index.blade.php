@extends('layouts.admin')

@section('title', 'Teachers Directory')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Teachers List</h5>
        <a href="{{ route('teachers.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add New</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Color Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ substr($teacher->name, 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $teacher->name }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>{{ $teacher->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ $teacher->department }}</span></td>
                        <td>
                            @if($teacher->color_code)
                            <span class="d-inline-block rounded-circle border" style="width: 20px; height: 20px; background-color: {{ $teacher->color_code }}"></span>
                            <small class="text-muted ms-1">{{ $teacher->color_code }}</small>
                            @else
                            <span class="text-muted fst-italic">None</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this teacher?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No teachers found. Click "Add New" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
