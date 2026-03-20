@extends('layouts.admin')

@section('title', 'Faculty Availability')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Availability Matrix</h5>
        <a href="{{ route('faculty-availabilities.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add Availability</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Day & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($availabilities as $avail)
                    <tr>
                        <td>{{ $avail->id }}</td>
                        <td class="fw-bold">
                            @if($avail->teacher)
                                {{ $avail->teacher->name }}
                            @else
                                <span class="text-danger fst-italic">Unknown Teacher</span>
                            @endif
                        </td>
                        <td>
                            @if($avail->timeslot)
                                <span class="badge bg-info text-dark">{{ $avail->timeslot->day }}</span>
                                <small class="text-muted ms-1">
                                    {{ \Carbon\Carbon::parse($avail->timeslot->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($avail->timeslot->end_time)->format('h:i A') }}
                                </small>
                            @else
                                <span class="text-danger fst-italic">Unknown Timeslot</span>
                            @endif
                        </td>
                        <td>
                            @if($avail->is_available)
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Available</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Unavailable</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('faculty-availabilities.edit', $avail) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('faculty-availabilities.destroy', $avail) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No availability records found. Click "Add Availability" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
