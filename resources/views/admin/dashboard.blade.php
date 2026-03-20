@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Teachers Card -->
    <div class="col-md-3 mt-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Teachers</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['teachers'] }}</h2>
                </div>
                <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('teachers.index') }}" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Subjects Card -->
    <div class="col-md-3 mt-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Subjects</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['subjects'] }}</h2>
                </div>
                <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                    <i class="fas fa-book fa-2x"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('subjects.index') }}" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="col-md-3 mt-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Courses</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['courses'] }}</h2>
                </div>
                <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                    <i class="fas fa-graduation-cap fa-2x"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('courses.index') }}" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Classrooms Card -->
    <div class="col-md-3 mt-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Classrooms</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['classrooms'] }}</h2>
                </div>
                <div class="p-3 bg-dark bg-opacity-10 rounded-circle">
                    <i class="fas fa-door-open fa-2x"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('classrooms.index') }}" class="text-dark text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <!-- Labs Card -->
    <div class="col-md-3 mt-4">
        <div class="card bg-danger text-white h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-uppercase mb-2 opacity-75">Labs</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['labs'] }}</h2>
                </div>
                <div class="p-3 bg-white bg-opacity-25 rounded-circle">
                    <i class="fas fa-desktop fa-2x"></i>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('classrooms.index') }}" class="text-white text-decoration-none small">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body text-center p-5">
                <img src="https://cdn-icons-png.flaticon.com/512/8150/8150531.png" width="150" alt="Timetable" class="mb-4 opacity-75">
                <h3 class="fw-bold text-primary">Ready to Generate Timetables?</h3>
                <p class="text-muted lead mb-4">Setup your datasets—courses, teachers, classrooms—and use our smart algorithm to instantly create a conflict-free schedule.</p>
                <a href="{{ route('timetable.index') }}" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill"><i class="fas fa-magic me-2"></i> Generate Timetable Now</a>
            </div>
        </div>
    </div>
</div>
@endsection
