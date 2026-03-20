<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Timetable;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Timeslot;
use App\Models\FacultyAvailability;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->query('term');   // odd|even|null
        $level = $request->query('level'); // UG|PG|null
        $teacherId = $request->query('teacher_id'); // for individual faculty view (optional)

        $timetables = Timetable::with(['course', 'subject', 'teacher', 'room', 'timeslot'])->get();

        // Optional filters
        $timetables = $timetables->filter(function ($t) use ($term, $level, $teacherId) {
            if ($teacherId && (int)$t->teacher_id !== (int)$teacherId) {
                return false;
            }

            $course = $t->course;
            if (!$course) return false;

            if ($term === 'odd' && !in_array($course->semester, [1, 3, 5, 7], true)) return false;
            if ($term === 'even' && !in_array($course->semester, [2, 4, 6, 8], true)) return false;

            if ($level && strtoupper($course->level ?? '') !== strtoupper($level)) return false;

            return true;
        });

        $byCourse = [];
        $byTeacher = [];
        $byRoom = [];
        $byLabRoom = [];

        foreach ($timetables as $t) {
            $courseName = optional($t->course)->name ?? 'N/A';
            $divisionLabel = $t->division ? ' (Div ' . $t->division . ')' : '';
            $courseLabel = $courseName . $divisionLabel;
            $teacherName = optional($t->teacher)->name ?? 'N/A';
            $roomName = optional($t->room)->room_name ?? 'N/A';
            $day = optional($t->timeslot)->day ?? 'N/A';

            $byCourse[$courseLabel][$day][] = $t;
            $byTeacher[$teacherName][$day][] = $t;
            $byRoom[$roomName][$day][] = $t;

            // Lab master: only lab rooms AND lab subjects
            if (
                $roomName !== 'N/A' &&
                stripos($roomName, 'lab') !== false &&
                $t->subject &&
                $t->subject->is_lab
            ) {
                $byLabRoom[$roomName][$day][] = $t;
            }
        }

        $teachers = Teacher::orderBy('department')->orderBy('name')->get();

        return view('admin.timetable.index', [
            'byCourse' => $byCourse,
            'byTeacher' => $byTeacher,
            'byRoom' => $byRoom,
            'byLabRoom' => $byLabRoom,
            'teachers' => $teachers,
            'selectedTerm' => $term,
            'selectedLevel' => $level,
            'selectedTeacherId' => $teacherId,
        ]);
    }

    public function generate(Request $request)
    {
        // Clear existing timetable
        Timetable::truncate();

        $courses = Course::with(['subjects.teacher'])->get();
        $classrooms = Classroom::all();
        $teachers = Teacher::all();
        $timeslots = Timeslot::where('is_active', true)->orderBy('day')->orderBy('start_time')->get();
        
        $errors = [];
        $scheduledCount = 0;

        if ($timeslots->isEmpty()) {
            return redirect()->route('timetable.index')->with('error', 'No active timeslots found. Please add/activate timeslots first.');
        }

        // Build timeslot ordering per day
        $dayOrder = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
        $timeslotsSorted = $timeslots->sortBy(function ($ts) use ($dayOrder) {
            return ($dayOrder[$ts->day] ?? 99) . '_' . $ts->start_time;
        })->values();

        $slotMeta = []; // timeslot_id => [day, idxInDay]
        $slotsByDay = [];
        foreach ($timeslotsSorted as $ts) {
            $slotsByDay[$ts->day][] = $ts;
        }
        foreach ($slotsByDay as $day => $list) {
            foreach (array_values($list) as $idx => $ts) {
                $slotMeta[$ts->id] = ['day' => $day, 'idx' => $idx];
            }
        }

        // Helpers for consecutive slots (for labs: 2-hour session)
        $consecutivePairs = []; // [day][timeslot_id] => next_timeslot_id
        foreach ($slotsByDay as $day => $list) {
            for ($i = 0; $i < count($list) - 1; $i++) {
                $a = $list[$i];
                $b = $list[$i + 1];
                // consecutive if end_time == next start_time
                if ($a->end_time === $b->start_time) {
                    $consecutivePairs[$day][$a->id] = $b->id;
                }
            }
        }

        // Occupancy sets (avoid conflicts)
        $occTeacher = []; // teacher_id|timeslot_id
        $occRoom = [];    // room_id|timeslot_id
        $occCourseDiv = []; // course_id|division|timeslot_id

        // Track teacher/day lecture streaks to allow max 2 back-to-back lectures
        $teacherDayLectureIdx = []; // teacher_id|day => set of idx values (lectures only)

        // Loop over each course
        foreach ($courses as $course) {
            $divisions = $course->division_list ?? ['A'];
            $isOddSemester = in_array($course->semester, [1, 3, 5, 7], true);

            foreach ($course->subjects as $subject) {
                // Enforce subject term vs course semester parity
                if ($subject->term === 'odd' && !$isOddSemester) {
                    continue;
                }
                if ($subject->term === 'even' && $isOddSemester) {
                    continue;
                }

                $isLab = (bool) $subject->is_lab;
                $assignedTeacherId = $subject->teacher_id;

                // For labs: 2-hour sessions => schedule in chunks of 2 consecutive slots
                $sessionsNeeded = $isLab ? (int) ceil($subject->hours_per_week / 2) : (int) $subject->hours_per_week;

                foreach ($divisions as $divisionName) {
                    for ($s = 0; $s < $sessionsNeeded; $s++) {
                        $assigned = false;

                        // Iterate slots in order to reduce gaps
                        foreach ($timeslotsSorted as $slot) {
                            $meta = $slotMeta[$slot->id] ?? null;
                            if (!$meta) continue;
                            $day = $meta['day'];
                            $idx = $meta['idx'];

                            // If lab, need a consecutive second slot
                            $slot2Id = null;
                            if ($isLab) {
                                $slot2Id = $consecutivePairs[$day][$slot->id] ?? null;
                                if (!$slot2Id) {
                                    continue;
                                }
                            }

                            // Course+division cannot have two classes at same time
                            $keyCD1 = $course->id.'|'.$divisionName.'|'.$slot->id;
                            if (isset($occCourseDiv[$keyCD1])) continue;
                            if ($isLab) {
                                $keyCD2 = $course->id.'|'.$divisionName.'|'.$slot2Id;
                                if (isset($occCourseDiv[$keyCD2])) continue;
                            }

                            // Choose room based on lab/theory
                            $freeRooms = $classrooms->filter(function ($room) use ($slot, $slot2Id, $isLab, $occRoom) {
                                $roomName = $room->room_name ?? '';
                                $isLabRoom = stripos($roomName, 'lab') !== false;
                                if ($isLab && !$isLabRoom) return false;
                                if (!$isLab && $isLabRoom) return false;

                                $k1 = $room->id.'|'.$slot->id;
                                if (isset($occRoom[$k1])) return false;
                                if ($slot2Id) {
                                    $k2 = $room->id.'|'.$slot2Id;
                                    if (isset($occRoom[$k2])) return false;
                                }
                                return true;
                            });
                            if ($freeRooms->isEmpty()) continue;

                            // Choose teacher: fixed teacher if assigned on subject; else any available
                            $candidateTeachers = $teachers;
                            if ($assignedTeacherId) {
                                $candidateTeachers = $teachers->where('id', $assignedTeacherId);
                            }

                            $chosenTeacher = null;
                            foreach ($candidateTeachers as $teacher) {
                                $kT1 = $teacher->id.'|'.$slot->id;
                                if (isset($occTeacher[$kT1])) continue;
                                if ($slot2Id) {
                                    $kT2 = $teacher->id.'|'.$slot2Id;
                                    if (isset($occTeacher[$kT2])) continue;
                                }

                                // Explicit unavailability
                                $explicitUnavail1 = FacultyAvailability::where('teacher_id', $teacher->id)
                                    ->where('timeslot_id', $slot->id)
                                    ->where('is_available', false)
                                    ->exists();
                                if ($explicitUnavail1) continue;
                                if ($slot2Id) {
                                    $explicitUnavail2 = FacultyAvailability::where('teacher_id', $teacher->id)
                                        ->where('timeslot_id', $slot2Id)
                                        ->where('is_available', false)
                                        ->exists();
                                    if ($explicitUnavail2) continue;
                                }

                                // Back-to-back lectures: allow max 2 consecutive lectures per teacher/day
                                if (!$isLab) {
                                    $dayKey = $teacher->id.'|'.$day;
                                    $idxSet = $teacherDayLectureIdx[$dayKey] ?? [];
                                    $idxSet[$idx] = true;
                                    // check if idx makes a streak of 3
                                    $streak = 1;
                                    $left = $idx - 1;
                                    while (isset($idxSet[$left])) { $streak++; $left--; }
                                    $right = $idx + 1;
                                    while (isset($idxSet[$right])) { $streak++; $right++; }
                                    if ($streak > 2) {
                                        continue;
                                    }
                                }

                                $chosenTeacher = $teacher;
                                break;
                            }
                            if (!$chosenTeacher) continue;

                            // Pick a room (first available keeps it deterministic)
                            $chosenRoom = $freeRooms->first();

                            // Book slot 1
                            Timetable::create([
                                'course_id' => $course->id,
                                'division' => $divisionName,
                                'subject_id' => $subject->id,
                                'teacher_id' => $chosenTeacher->id,
                                'room_id' => $chosenRoom->id,
                                'timeslot_id' => $slot->id,
                            ]);

                            $occCourseDiv[$course->id.'|'.$divisionName.'|'.$slot->id] = true;
                            $occRoom[$chosenRoom->id.'|'.$slot->id] = true;
                            $occTeacher[$chosenTeacher->id.'|'.$slot->id] = true;
                            if (!$isLab) {
                                $teacherDayLectureIdx[$chosenTeacher->id.'|'.$day][$idx] = true;
                            }

                            // Book slot 2 for lab session
                            if ($isLab && $slot2Id) {
                                Timetable::create([
                                    'course_id' => $course->id,
                                    'division' => $divisionName,
                                    'subject_id' => $subject->id,
                                    'teacher_id' => $chosenTeacher->id,
                                    'room_id' => $chosenRoom->id,
                                    'timeslot_id' => $slot2Id,
                                ]);
                                $occCourseDiv[$course->id.'|'.$divisionName.'|'.$slot2Id] = true;
                                $occRoom[$chosenRoom->id.'|'.$slot2Id] = true;
                                $occTeacher[$chosenTeacher->id.'|'.$slot2Id] = true;
                                $scheduledCount++;
                            }

                            $assigned = true;
                            $scheduledCount++;
                            break;
                        }

                        if (!$assigned) {
                            $typeLabel = $isLab ? 'lab session' : 'lecture';
                            $errors[] = "Could not schedule 1 {$typeLabel} for {$subject->subject_name} ({$course->name} - Div {$divisionName}). Not enough free slots/rooms/teachers.";
                        }
                    }
                }
            }
        }

        if (count($errors) > 0) {
            return redirect()->route('timetable.index')->with('error', 'Timetable partially generated with conflicts: ' . implode(" ", $errors));
        }

        return redirect()->route('timetable.index')->with('success', "Timetable generated successfully! ($scheduledCount slots booked)");
    }

    public function export(Request $request, string $type)
    {
        $timetables = Timetable::with(['course', 'subject', 'teacher', 'room', 'timeslot'])->get();

        if ($timetables->isEmpty()) {
            return redirect()->route('timetable.index')->with('error', 'No timetable data available to export.');
        }

        $type = strtolower($type);
        $filePrefix = match ($type) {
            'division' => 'division_master',
            'lab' => 'lab_master',
            'faculty' => 'faculty_master',
            'class' => 'class_master',
            default => 'timetable',
        };

        // Optional: export a single faculty timetable
        if ($type === 'faculty' && $request->filled('teacher_id')) {
            $teacherId = (int) $request->query('teacher_id');
            $timetables = $timetables->where('teacher_id', $teacherId);
            $filePrefix = 'faculty_' . $teacherId;
        }

        // Filter for lab master: only lab rooms AND lab subjects
        if ($type === 'lab') {
            $timetables = $timetables->filter(function ($slot) {
                $roomName = optional($slot->room)->room_name;
                return $roomName && stripos($roomName, 'lab') !== false && $slot->subject && $slot->subject->is_lab;
            });

            if ($timetables->isEmpty()) {
                return redirect()->route('timetable.index')->with('error', 'No lab timetable data available to export.');
            }
        }

        $fileName = $filePrefix . '_' . now()->format('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($timetables, $type) {
            $handle = fopen('php://output', 'w');

            $firstColumnTitle = match ($type) {
                'division' => 'Course',
                'lab', 'class' => 'Room',
                'faculty' => 'Teacher',
                default => 'Course',
            };

            fputcsv($handle, [
                $firstColumnTitle,
                'Division',
                'Day',
                'Start Time',
                'End Time',
                'Subject',
                'Type',
                'Teacher',
                'Room',
            ]);

            foreach ($timetables as $slot) {
                $firstColumnValue = match ($type) {
                    'division' => optional($slot->course)->name,
                    'lab', 'class' => optional($slot->room)->room_name,
                    'faculty' => optional($slot->teacher)->name,
                    default => optional($slot->course)->name,
                };

                $typeLabel = $slot->subject && $slot->subject->is_lab ? 'Lab' : 'Lecture';

                fputcsv($handle, [
                    $firstColumnValue,
                    $slot->division,
                    optional($slot->timeslot)->day,
                    optional($slot->timeslot)->start_time,
                    optional($slot->timeslot)->end_time,
                    optional($slot->subject)->subject_name,
                    $typeLabel,
                    optional($slot->teacher)->name,
                    optional($slot->room)->room_name,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
