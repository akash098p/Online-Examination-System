<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Result;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $studentSemester = $user->semester;
        $studentDepartment = $user->department;

        $examScope = function ($query) use ($studentSemester, $studentDepartment) {
            if (! empty($studentSemester)) {
                Exam::applyAcademicFieldFilter($query, 'semester', [$studentSemester], true);
            }

            if (! empty($studentDepartment)) {
                Exam::applyAcademicFieldFilter($query, 'department', [$studentDepartment], true);
            }
        };

        // ✅ Total exams in system
        $totalExams = Exam::where('status', 'published')
            ->where($examScope)
            ->count();

        // ✅ Exams already attempted by this student
        $attemptedExamIds = Result::where('user_id', $user->id)->pluck('exam_id');

        // ✅ Upcoming exams (not attempted + future + student semester match)
        $nextExams = Exam::withCount('questions')
            ->where('status', 'published')
            ->where('start_time', '>', now())
            ->whereNotIn('id', $attemptedExamIds)
            ->where($examScope)
            ->orderBy('start_time', 'asc')
            ->get();

        // ✅ Upcoming exams count
        $upcomingExams = $nextExams->count();

        // ✅ Completed exams count
        $completedExams = Result::where('user_id', $user->id)->count();

        // ✅ Average score
        $averageScore = Result::where('user_id', $user->id)->avg('percentage');

        // 🏆 Semester-matched Top Performing Students
        $topStudents = Result::with('user')
            ->whereHas('user', function ($query) use ($studentSemester, $studentDepartment) {
                if (! empty($studentSemester)) {
                    $query->where('semester', $studentSemester);
                }

                if (! empty($studentDepartment)) {
                    $query->where('department', $studentDepartment);
                }
            })
            ->selectRaw('user_id, AVG(percentage) as avg_percentage')
            ->groupBy('user_id')
            ->orderByDesc('avg_percentage')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'totalExams',
            'upcomingExams',
            'completedExams',
            'averageScore',
            'nextExams',
            'topStudents' // 👈 ADDED ONLY
        ));
    }
}
