@extends('layouts.admin')

@section('title', 'Classrooms Directory')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Classrooms List</h5>
        <a href="{{ route('classrooms.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add New</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Room Name / Number</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $classroom)
                    <tr>
                        <td>{{ $classroom->id }}</td>
                        <td class="fw-bold">
                            <i class="fas fa-door-open text-muted me-2"></i> {{ $classroom->room_name }}
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $classroom->capacity }} Seats</span>
                        </td>
                        <td>
                            <a href="{{ route('classrooms.edit', $classroom) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('classrooms.destroy', $classroom) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this classroom?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No classrooms found. Click "Add New" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
