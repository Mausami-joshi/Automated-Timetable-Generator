@extends('layouts.admin')

@section('title', 'Generated Timetables')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h4 class="text-primary mb-0">Master Timetables</h4>
                <p class="text-muted mb-0">View and export timetables by division, lab, faculty, and class.</p>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('timetable.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
                    <select name="term" class="form-select form-select-sm" style="min-width: 160px;">
                        <option value="">All Terms</option>
                        <option value="odd" {{ ($selectedTerm ?? '') === 'odd' ? 'selected' : '' }}>Odd (1,3,5,7)</option>
                        <option value="even" {{ ($selectedTerm ?? '') === 'even' ? 'selected' : '' }}>Even (2,4,6,8)</option>
                    </select>

                    <select name="level" class="form-select form-select-sm" style="min-width: 120px;">
                        <option value="">UG + PG</option>
                        <option value="UG" {{ ($selectedLevel ?? '') === 'UG' ? 'selected' : '' }}>UG</option>
                        <option value="PG" {{ ($selectedLevel ?? '') === 'PG' ? 'selected' : '' }}>PG</option>
                    </select>

                    <select name="teacher_id" class="form-select form-select-sm" style="min-width: 220px;">
                        <option value="">All Faculty</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ (string)($selectedTeacherId ?? '') === (string)$t->id ? 'selected' : '' }}>
                                {{ $t->department }} - {{ $t->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary">Apply</button>
                    <a href="{{ route('timetable.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </form>

                <form action="{{ route('timetable.generate') }}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: Generating a new timetable will overwrite the existing one. Proceed?');">
                    @csrf
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-magic me-2"></i> Auto-Generate Timetable</button>
                </form>

                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle shadow-sm" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-print me-2"></i> Export / Download
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('timetable.export', 'division') }}">Division Master (by Course)</a></li>
                        <li><a class="dropdown-item" href="{{ route('timetable.export', 'lab') }}">Lab Master (by Lab Room)</a></li>
                        <li><a class="dropdown-item" href="{{ route('timetable.export', 'faculty') }}">Faculty Master (by Teacher)</a></li>
                        @if(!empty($selectedTeacherId))
                            <li><a class="dropdown-item" href="{{ route('timetable.export', ['type' => 'faculty', 'teacher_id' => $selectedTeacherId]) }}">Individual Faculty (selected)</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('timetable.export', 'class') }}">Class Master (by Classroom)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $hasData = !empty($byCourse) || !empty($byTeacher) || !empty($byRoom) || !empty($byLabRoom);
    $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
@endphp

@if(!$hasData)
<div class="card shadow-sm border-0">
    <div class="card-body text-center p-5">
        <i class="fas fa-calendar-times fa-4x text-muted mb-3 opacity-50"></i>
        <h5>No Timetable Generated Yet</h5>
        <p class="text-muted mb-0">Click the "Auto-Generate Timetable" button above to run the scheduling algorithm.</p>
    </div>
</div>
@else

    <ul class="nav nav-tabs mb-3" id="masterTimetableTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="division-tab" data-bs-toggle="tab" data-bs-target="#division" type="button" role="tab" aria-controls="division" aria-selected="true">
                Division Master
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="lab-tab" data-bs-toggle="tab" data-bs-target="#lab" type="button" role="tab" aria-controls="lab" aria-selected="false">
                Lab Master
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="faculty-tab" data-bs-toggle="tab" data-bs-target="#faculty" type="button" role="tab" aria-controls="faculty" aria-selected="false">
                Faculty Master
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="class-tab" data-bs-toggle="tab" data-bs-target="#class" type="button" role="tab" aria-controls="class" aria-selected="false">
                Class Master
            </button>
        </li>
    </ul>

    <div class="tab-content" id="masterTimetableTabsContent">
        {{-- Division Master (by Course) --}}
        <div class="tab-pane fade show active" id="division" role="tabpanel" aria-labelledby="division-tab">
            @foreach($byCourse as $courseName => $days)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary"><i class="fas fa-graduation-cap me-2"></i> Course: {{ $courseName }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th>Scheduled Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $dayOfWeek)
                                        @if(isset($days[$dayOfWeek]))
                                            <tr>
                                                <td class="fw-bold bg-light">{{ $dayOfWeek }}</td>
                                                <td class="text-start">
                                                    <div class="d-flex flex-wrap gap-2 p-2">
                                                        @foreach($days[$dayOfWeek] as $slot)
                                                            <div class="card border-0 shadow-sm" style="min-width: 200px; border-left: 4px solid {{ $slot->teacher->color_code ?? '#2a5298' }} !important;">
                                                                <div class="card-body p-2">
                                                                    <div class="small fw-bold text-primary mb-1">
                                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($slot->timeslot->start_time)->format('h:i A') }} -
                                                                        {{ \Carbon\Carbon::parse($slot->timeslot->end_time)->format('h:i A') }}
                                                                    </div>
                                                                    <div class="fw-bold mb-1">
                                                                        {{ $slot->subject->subject_name }}
                                                                        @if($slot->subject->is_lab)
                                                                            <span class="badge bg-danger ms-1">Lab</span>
                                                                        @else
                                                                            <span class="badge bg-secondary ms-1">Lecture</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted mb-1"><i class="fas fa-user-tie"></i> {{ $slot->teacher->name }}</div>
                                                                    <div class="small text-muted"><i class="fas fa-map-marker-alt"></i> {{ $slot->room->room_name }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Lab Master (by Lab Room) --}}
        <div class="tab-pane fade" id="lab" role="tabpanel" aria-labelledby="lab-tab">
            @forelse($byLabRoom as $roomName => $days)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary"><i class="fas fa-flask me-2"></i> Lab: {{ $roomName }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th>Scheduled Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $dayOfWeek)
                                        @if(isset($days[$dayOfWeek]))
                                            <tr>
                                                <td class="fw-bold bg-light">{{ $dayOfWeek }}</td>
                                                <td class="text-start">
                                                    <div class="d-flex flex-wrap gap-2 p-2">
                                                        @foreach($days[$dayOfWeek] as $slot)
                                                            <div class="card border-0 shadow-sm" style="min-width: 200px; border-left: 4px solid {{ $slot->teacher->color_code ?? '#2a5298' }} !important;">
                                                                <div class="card-body p-2">
                                                                    <div class="small fw-bold text-primary mb-1">
                                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($slot->timeslot->start_time)->format('h:i A') }} -
                                                                        {{ \Carbon\Carbon::parse($slot->timeslot->end_time)->format('h:i A') }}
                                                                    </div>
                                                                    <div class="fw-bold mb-1">
                                                                        {{ $slot->subject->subject_name }}
                                                                        @if($slot->subject->is_lab)
                                                                            <span class="badge bg-danger ms-1">Lab</span>
                                                                        @else
                                                                            <span class="badge bg-secondary ms-1">Lecture</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted mb-1"><i class="fas fa-user-tie"></i> {{ $slot->teacher->name }}</div>
                                                                    <div class="small text-muted"><i class="fas fa-graduation-cap"></i> {{ $slot->course->name }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">No lab rooms found (rooms containing the word \"Lab\").</div>
            @endforelse
        </div>

        {{-- Faculty Master (by Teacher) --}}
        <div class="tab-pane fade" id="faculty" role="tabpanel" aria-labelledby="faculty-tab">
            @foreach($byTeacher as $teacherName => $days)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary"><i class="fas fa-user-tie me-2"></i> Faculty: {{ $teacherName }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th>Scheduled Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $dayOfWeek)
                                        @if(isset($days[$dayOfWeek]))
                                            <tr>
                                                <td class="fw-bold bg-light">{{ $dayOfWeek }}</td>
                                                <td class="text-start">
                                                    <div class="d-flex flex-wrap gap-2 p-2">
                                                        @foreach($days[$dayOfWeek] as $slot)
                                                            <div class="card border-0 shadow-sm" style="min-width: 200px; border-left: 4px solid {{ $slot->teacher->color_code ?? '#2a5298' }} !important;">
                                                                <div class="card-body p-2">
                                                                    <div class="small fw-bold text-primary mb-1">
                                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($slot->timeslot->start_time)->format('h:i A') }} -
                                                                        {{ \Carbon\Carbon::parse($slot->timeslot->end_time)->format('h:i A') }}
                                                                    </div>
                                                                    <div class="fw-bold mb-1">
                                                                        {{ $slot->subject->subject_name }}
                                                                        @if($slot->subject->is_lab)
                                                                            <span class="badge bg-danger ms-1">Lab</span>
                                                                        @else
                                                                            <span class="badge bg-secondary ms-1">Lecture</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted mb-1"><i class="fas fa-graduation-cap"></i> {{ $slot->course->name }}</div>
                                                                    <div class="small text-muted"><i class="fas fa-map-marker-alt"></i> {{ $slot->room->room_name }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Class Master (by Classroom) --}}
        <div class="tab-pane fade" id="class" role="tabpanel" aria-labelledby="class-tab">
            @foreach($byRoom as $roomName => $days)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary"><i class="fas fa-door-open me-2"></i> Classroom: {{ $roomName }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Day</th>
                                        <th>Scheduled Classes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daysOfWeek as $dayOfWeek)
                                        @if(isset($days[$dayOfWeek]))
                                            <tr>
                                                <td class="fw-bold bg-light">{{ $dayOfWeek }}</td>
                                                <td class="text-start">
                                                    <div class="d-flex flex-wrap gap-2 p-2">
                                                        @foreach($days[$dayOfWeek] as $slot)
                                                            <div class="card border-0 shadow-sm" style="min-width: 200px; border-left: 4px solid {{ $slot->teacher->color_code ?? '#2a5298' }} !important;">
                                                                <div class="card-body p-2">
                                                                    <div class="small fw-bold text-primary mb-1">
                                                                        <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($slot->timeslot->start_time)->format('h:i A') }} -
                                                                        {{ \Carbon\Carbon::parse($slot->timeslot->end_time)->format('h:i A') }}
                                                                    </div>
                                                                    <div class="fw-bold mb-1">
                                                                        {{ $slot->subject->subject_name }}
                                                                        @if($slot->subject->is_lab)
                                                                            <span class="badge bg-danger ms-1">Lab</span>
                                                                        @else
                                                                            <span class="badge bg-secondary ms-1">Lecture</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="small text-muted mb-1"><i class="fas fa-user-tie"></i> {{ $slot->teacher->name }}</div>
                                                                    <div class="small text-muted"><i class="fas fa-graduation-cap"></i> {{ $slot->course->name }}</div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endif

@endsection
