@extends('layouts.admin')

@section('title', 'Edit Subject')

@section('content')
<div class="card shadow-sm border-0 col-md-8 mx-auto">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary">Edit Subject: {{ $subject->subject_name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('subjects.update', $subject) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label fw-bold">Subject Name</label>
                <input type="text" name="subject_name" class="form-control @error('subject_name') is-invalid @enderror" value="{{ old('subject_name', $subject->subject_name) }}" required>
                @error('subject_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Hours Per Week</label>
                <input type="number" name="hours_per_week" class="form-control @error('hours_per_week') is-invalid @enderror" value="{{ old('hours_per_week', $subject->hours_per_week) }}" min="1" max="40" required>
                @error('hours_per_week') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Term</label>
                <select name="term" id="term" class="form-select @error('term') is-invalid @enderror" required>
                    <option value="odd" {{ old('term', $subject->term) == 'odd' ? 'selected' : '' }}>Odd (1,3,5,7)</option>
                    <option value="even" {{ old('term', $subject->term) == 'even' ? 'selected' : '' }}>Even (2,4,6,8)</option>
                </select>
                @error('term') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Semester</label>
                <select id="semester" class="form-select" required>
                    <option value="" selected disabled>Select Semester</option>
                    @php
                        $semesters = $courses->pluck('semester')->unique()->sort()->values();
                    @endphp
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}">{{ $sem }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Course</label>
                <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                                data-semester="{{ $course->semester }}"
                                {{ old('course_id', $subject->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} (Semester {{ $course->semester }})
                        </option>
                    @endforeach
                </select>
                @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Assigned Faculty (for both theory & lab)</label>
                <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                    <option value="">Auto-pick during generation</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->department }} - {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4 form-check">
                <input class="form-check-input" type="checkbox" name="is_lab" id="is_lab" value="1" {{ old('is_lab', $subject->is_lab) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="is_lab">
                    This subject is a Lab
                </label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('subjects.index') }}" class="btn btn-light border">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Subject</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const termEl = document.getElementById('term');
        const semesterEl = document.getElementById('semester');
        const courseEl = document.getElementById('course_id');

        function allowedSemestersForTerm(term) {
            if (term === 'even') return new Set(['2','4','6','8']);
            return new Set(['1','3','5','7']);
        }

        function filterSemesterOptions() {
            const allowed = allowedSemestersForTerm(termEl.value);
            let hasSelected = false;

            Array.from(semesterEl.options).forEach(opt => {
                if (!opt.value) return;
                const ok = allowed.has(String(opt.value));
                opt.hidden = !ok;
                opt.disabled = !ok;
                if (semesterEl.value === opt.value && ok) hasSelected = true;
            });

            if (!hasSelected) {
                semesterEl.value = '';
            }
        }

        function filterCourses() {
            const semester = semesterEl.value;
            Array.from(courseEl.options).forEach(opt => {
                if (!opt.value) return;
                const ok = opt.dataset.semester === semester;
                opt.hidden = !ok;
                opt.disabled = !ok;
            });

            const selectedOpt = courseEl.options[courseEl.selectedIndex];
            if (selectedOpt && selectedOpt.value && (selectedOpt.disabled || selectedOpt.hidden)) {
                courseEl.value = '';
            }
        }

        termEl.addEventListener('change', () => {
            filterSemesterOptions();
            filterCourses();
        });
        semesterEl.addEventListener('change', filterCourses);

        // initial filter
        filterSemesterOptions();
        const preSelected = courseEl.options[courseEl.selectedIndex];
        if (preSelected && preSelected.value && preSelected.dataset.semester) {
            semesterEl.value = preSelected.dataset.semester;
        }
        filterCourses();
    });
</script>
@endsection
