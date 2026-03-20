@extends('layouts.admin')

@section('title', 'Timeslots Directory')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary">Timeslots List</h5>
        <a href="{{ route('timeslots.create') }}" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i> Add New</a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Define the standard class timings for each day of the week.
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Day of Week</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timeslots as $timeslot)
                    <tr>
                        <td>{{ $timeslot->id }}</td>
                        <td class="fw-bold">{{ $timeslot->day }}</td>
                        <td><span class="badge bg-success">{{ \Carbon\Carbon::parse($timeslot->start_time)->format('h:i A') }}</span></td>
                        <td><span class="badge bg-danger">{{ \Carbon\Carbon::parse($timeslot->end_time)->format('h:i A') }}</span></td>
                        <td>
                            @php
                                $start = \Carbon\Carbon::parse($timeslot->start_time);
                                $end = \Carbon\Carbon::parse($timeslot->end_time);
                                $diff = $start->diffInMinutes($end);
                            @endphp
                            <span class="text-muted">{{ $diff }} mins</span>
                        </td>
                        <td>
                            @if($timeslot->is_active)
                                <span class="badge bg-primary">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('timeslots.edit', $timeslot) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('timeslots.destroy', $timeslot) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this timeslot?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No timeslots found. Click "Add New" to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
