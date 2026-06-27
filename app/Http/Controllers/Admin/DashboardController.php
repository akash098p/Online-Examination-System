<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Response;
use App\Models\Result;
use App\Models\StudentProfileChangeRequest;
use App\Models\User;
use App\Services\GeminiAiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Existing counts
        $totalExams = Exam::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalAttempts = Result::count();
        $recentExams = Exam::latest()->take(5)->get();

        // NEW Real-Time Stats (No logic changed)
        $activeExams = Exam::where('status', 'published')->count();
        $draftExams = Exam::where('status', 'draft')->count();
        $totalResults = Result::count();
        $pendingProfileRequestCount = StudentProfileChangeRequest::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalExams',
            'totalStudents',
            'totalAttempts',
            'recentExams',
            'activeExams',
            'draftExams',
            'totalResults',
            'pendingProfileRequestCount'
        ));
    }

    public function profileChangeRequests()
    {
        $requests = StudentProfileChangeRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(12);

        return view('admin.profile-requests.index', compact('requests'));
    }

    public function approveProfileChangeRequest(StudentProfileChangeRequest $profileRequest)
    {
        abort_unless($profileRequest->status === 'pending', 404);

        $student = $profileRequest->user;

        if ($student) {
            $student->department = $profileRequest->requested_department;
            $student->semester = $profileRequest->requested_semester;
            $student->save();
        }

        $profileRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Student academic change request approved.');
    }

    public function rejectProfileChangeRequest(StudentProfileChangeRequest $profileRequest)
    {
        abort_unless($profileRequest->status === 'pending', 404);

        $profileRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Student academic change request rejected.');
    }

    public function analytics(Request $request, GeminiAiService $geminiAiService)
    {
        $selectedDepartments = $this->normalizeMultiSelectFilter(
            (array) $request->query('department', []),
            (array) config('academix.departments', [])
        );
        $selectedSemesters = $this->normalizeMultiSelectFilter(
            (array) $request->query('semester', []),
            (array) config('academix.semesters', [])
        );

        // ----- 1. Exam Summary -----
        $examSummary = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->select(
                'exams.title as exam_title',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('AVG(results.percentage) as avg_percentage'),
                DB::raw('SUM(CASE WHEN results.status="Pass" THEN 1 ELSE 0 END) as passed')
            )
            ->when(count($selectedDepartments) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.department', $selectedDepartments, true))
            ->when(count($selectedSemesters) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.semester', $selectedSemesters, true))
            ->groupBy('exams.id', 'exams.title')
            ->get();

        $examLabels = [];
        $examScores = [];

        foreach ($examSummary as $row) {
            $examLabels[] = $row->exam_title;
            $examScores[] = round($row->avg_percentage, 2);
        }

        // ----- 2. Daily Activity -----
        $dailyData = DB::table('results')
            ->join('exams', 'exams.id', '=', 'results.exam_id')
            ->select(DB::raw('DATE(results.created_at) as day'), DB::raw('COUNT(*) as count'))
            ->when(count($selectedDepartments) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.department', $selectedDepartments, true))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $dailyLabels = $dailyData->pluck('day');
        $dailyCounts = $dailyData->pluck('count');

        // ----- 2b. Daily attempt details for popup -----
        $dailyAttemptDetails = Result::with(['user:id,name,profile_photo,sex,registration_no,department,semester', 'exam:id,title,department,semester'])
            ->select('id', 'exam_id', 'user_id', 'percentage', 'status', 'created_at')
            ->when(count($selectedDepartments) > 0, function ($query) use ($selectedDepartments) {
                $query->whereHas('exam', fn ($examQuery) => Exam::applyAcademicFieldFilter($examQuery, 'department', $selectedDepartments, true));
            })
            ->latest('created_at')
            ->get()
            ->groupBy(function ($row) {
                return $row->created_at->format('Y-m-d');
            })
            ->map(function ($items) {
                return $items->map(function ($r) {
                    return [
                        'student_name' => $r->user?->name ?? 'Unknown',
                        'student_reg' => $r->user?->registration_no ?? 'N/A',
                        'student_department' => $r->user?->department ?? 'N/A',
                        'student_semester' => $r->user?->semester ?? 'N/A',
                        'student_photo' => $r->user?->profilePhotoUrl() ?? null,
                        'exam_title' => $r->exam?->title ?? 'Exam',
                        'percentage' => round((float) $r->percentage, 2),
                        'status' => $r->status,
                        'time' => optional($r->created_at)->format('d M Y, h:i A'),
                    ];
                })->values();
            });

        // ----- 3. Top Students -----
        $top = DB::table('results')
            ->join('users', 'users.id', '=', 'results.user_id')
            ->select('results.user_id', DB::raw('AVG(results.percentage) as avg_percentage'))
            ->when(count($selectedDepartments) > 0, fn ($query) => $query->whereIn('users.department', $selectedDepartments))
            ->when(count($selectedSemesters) > 0, fn ($query) => $query->whereIn('users.semester', $selectedSemesters))
            ->groupBy('results.user_id')
            ->orderByDesc('avg_percentage')
            ->limit(10)
            ->get();

        $topStudents = [];

        foreach ($top as $row) {
            $user = User::withTrashed()->find($row->user_id);
            if ($user) {
                $topStudents[] = (object)[
                    'user' => $user,
                    'avg_percentage' => round($row->avg_percentage, 2)
                ];
            }
        }

        $weakTopicsData = Response::query()
            ->join('questions', 'questions.id', '=', 'responses.question_id')
            ->join('exams', 'exams.id', '=', 'responses.exam_id')
            ->selectRaw("COALESCE(NULLIF(MAX(questions.topic), ''), NULLIF(MAX(exams.subject), ''), 'General') as topic")
            ->selectRaw('COUNT(responses.id) as attempts')
            ->selectRaw('SUM(CASE WHEN responses.is_correct = 1 THEN 1 ELSE 0 END) as correct_count')
            ->when(count($selectedDepartments) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.department', $selectedDepartments, true))
            ->when(count($selectedSemesters) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.semester', $selectedSemesters, true))
            ->groupBy('questions.topic', 'exams.subject')
            ->havingRaw('COUNT(responses.id) > 0')
            ->orderByRaw('(SUM(CASE WHEN responses.is_correct = 1 THEN 1 ELSE 0 END) / COUNT(responses.id)) asc')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $accuracy = $row->attempts > 0 ? round(($row->correct_count / $row->attempts) * 100, 2) : 0;

                return [
                    'topic' => $row->topic,
                    'accuracy' => $accuracy,
                    'attempts' => (int) $row->attempts,
                ];
            });

        $hardestQuestions = Response::query()
            ->join('questions', 'questions.id', '=', 'responses.question_id')
            ->join('exams', 'exams.id', '=', 'responses.exam_id')
            ->select(
                'questions.question_text',
                'questions.topic',
                'exams.title as exam_title'
            )
            ->selectRaw('COUNT(responses.id) as attempts')
            ->selectRaw('SUM(CASE WHEN responses.is_correct = 0 THEN 1 ELSE 0 END) as wrong_count')
            ->when(count($selectedDepartments) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.department', $selectedDepartments, true))
            ->when(count($selectedSemesters) > 0, fn ($query) => Exam::applyAcademicFieldFilter($query, 'exams.semester', $selectedSemesters, true))
            ->groupBy('questions.id', 'questions.question_text', 'questions.topic', 'exams.id', 'exams.title')
            ->havingRaw('COUNT(responses.id) >= 1')
            ->orderByRaw('(SUM(CASE WHEN responses.is_correct = 0 THEN 1 ELSE 0 END) / COUNT(responses.id)) desc')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                $wrongRate = $row->attempts > 0 ? round(($row->wrong_count / $row->attempts) * 100, 2) : 0;

                return (object) [
                    'question_text' => $row->question_text,
                    'topic' => $row->topic ?: 'General',
                    'exam_title' => $row->exam_title,
                    'attempts' => (int) $row->attempts,
                    'wrong_rate' => $wrongRate,
                ];
            });

        $analyticsPayload = [
            'filters' => [
                'department' => count($selectedDepartments) > 0 ? implode(', ', $selectedDepartments) : 'All Departments',
                'semester' => count($selectedSemesters) > 0 ? implode(', ', $selectedSemesters) : 'All Semesters',
            ],
            'exam_summary' => collect($examSummary)->map(fn ($row) => [
                'exam' => $row->exam_title,
                'attempts' => (int) $row->attempts,
                'avg_percentage' => round((float) $row->avg_percentage, 2),
                'passed' => (int) $row->passed,
            ])->values()->all(),
            'daily_attempts' => collect($dailyData)->map(fn ($row) => [
                'day' => $row->day,
                'count' => (int) $row->count,
            ])->values()->all(),
            'weak_topics' => $weakTopicsData->all(),
            'hardest_questions' => $hardestQuestions->map(fn ($row) => [
                'question' => $row->question_text,
                'topic' => $row->topic,
                'exam' => $row->exam_title,
                'wrong_rate' => $row->wrong_rate,
            ])->all(),
        ];

        $insightCacheKey = 'admin:analytics:ai-insights:'.sha1(json_encode($analyticsPayload));

        $aiInsights = Cache::remember($insightCacheKey, now()->addMinutes((int) config('services.gemini.analytics_cache_minutes', 60)), function () use ($analyticsPayload, $geminiAiService) {
            try {
                return $geminiAiService->generateAnalyticsInsights([
                    ...$analyticsPayload,
                    'cache_key' => 'ai:analytics:'.sha1(json_encode($analyticsPayload)),
                ]);
            } catch (\Throwable $e) {
                $weakTopic = $analyticsPayload['weak_topics'][0]['topic'] ?? 'General topics';
                $hardQuestion = $analyticsPayload['hardest_questions'][0]['question'] ?? 'No dominant hard question yet';

                return [
                    'headline' => 'AI insights unavailable right now',
                    'summary' => 'Showing a safe local summary based on current analytics data.',
                    'insights' => [
                        "Lowest-performing topic currently looks like {$weakTopic}.",
                        "A difficult question trend is forming around: {$hardQuestion}.",
                        'Review recent daily attempts to confirm whether performance is improving or slipping.',
                    ],
                    'actions' => [
                        'Revise weak topics with targeted practice questions.',
                        'Audit difficult questions for clarity and syllabus alignment.',
                    ],
                ];
            }
        });

        return view('admin.analytics', compact(
            'examLabels',
            'examScores',
            'dailyLabels',
            'dailyCounts',
            'topStudents',
            'dailyAttemptDetails',
            'weakTopicsData',
            'hardestQuestions',
            'aiInsights',
            'selectedDepartments',
            'selectedSemesters'
        ));
    }

    protected function normalizeMultiSelectFilter(array $selectedValues, array $allOptions): array
    {
        $selectedValues = array_values(array_unique(array_filter(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $selectedValues), fn ($value) => $value !== '' && $value !== null)));
        $allOptions = array_values(array_unique(array_filter(array_map(function ($value) {
            return is_string($value) ? trim($value) : $value;
        }, $allOptions), fn ($value) => $value !== '' && $value !== null)));

        if (count($selectedValues) === 0) {
            return [];
        }

        sort($selectedValues);
        sort($allOptions);

        if ($selectedValues === $allOptions) {
            return [];
        }

        return $selectedValues;
    }
}
