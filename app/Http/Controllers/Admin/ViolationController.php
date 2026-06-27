<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $examSummaries = Exam::query()
            ->whereHas('violations')
            ->withCount('violations')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            })
            ->addSelect([
                'latest_violation_at' => Violation::select('created_at')
                    ->whereColumn('exam_id', 'exams.id')
                    ->latest()
                    ->limit(1),
            ])
            ->orderByDesc('latest_violation_at')
            ->get();

        $recentViolations = Violation::with(['exam:id,title,subject', 'user:id,name,email,registration_no'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('exam', function ($examQuery) use ($search) {
                        $examQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('registration_no', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->limit(10)
            ->get();

        $reasonHighlights = Violation::query()
            ->select([
                'reason',
                DB::raw('COUNT(*) as total'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('reason', 'like', "%{$search}%")
                    ->orWhereHas('exam', function ($examQuery) use ($search) {
                        $examQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('subject', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('registration_no', 'like', "%{$search}%");
                    });
            })
            ->groupBy('reason')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $stats = [
            'total_violations' => Violation::count(),
            'affected_exams' => Exam::whereHas('violations')->count(),
            'affected_students' => Violation::distinct('user_id')->count('user_id'),
        ];

        return view('admin.violations.index', compact('examSummaries', 'recentViolations', 'reasonHighlights', 'search', 'stats'));
    }

    public function exam(Request $request, Exam $exam)
    {
        $reason = trim((string) $request->query('reason', ''));

        $studentSummaries = Violation::query()
            ->select([
                'user_id',
                DB::raw('COUNT(*) as violations_count'),
                DB::raw('MAX(created_at) as latest_violation_at'),
            ])
            ->where('exam_id', $exam->id)
            ->when($reason !== '', function ($query) use ($reason) {
                $query->where('reason', 'like', "%{$reason}%");
            })
            ->groupBy('user_id')
            ->orderByDesc('latest_violation_at')
            ->get();

        $users = User::withTrashed()
            ->whereIn('id', $studentSummaries->pluck('user_id'))
            ->get()
            ->keyBy('id');

        $studentSummaries = $studentSummaries->map(function ($summary) use ($users) {
            $summary->user = $users->get($summary->user_id);
            return $summary;
        })->filter(fn ($summary) => $summary->user !== null)->values();

        $recentViolations = Violation::with(['user:id,name,email,registration_no'])
            ->where('exam_id', $exam->id)
            ->when($reason !== '', function ($query) use ($reason) {
                $query->where('reason', 'like', "%{$reason}%");
            })
            ->latest()
            ->limit(16)
            ->get();

        return view('admin.violations.exam', compact('exam', 'studentSummaries', 'recentViolations', 'reason'));
    }

    public function studentOverview(int $userId)
    {
        $student = User::withTrashed()->findOrFail($userId);

        $examSummaries = Violation::query()
            ->select([
                'exam_id',
                DB::raw('COUNT(*) as violations_count'),
                DB::raw('MAX(created_at) as latest_violation_at'),
            ])
            ->where('user_id', $student->id)
            ->groupBy('exam_id')
            ->orderByDesc('latest_violation_at')
            ->get();

        $exams = Exam::whereIn('id', $examSummaries->pluck('exam_id'))
            ->get()
            ->keyBy('id');

        $examSummaries = $examSummaries->map(function ($summary) use ($exams) {
            $summary->exam = $exams->get($summary->exam_id);
            return $summary;
        })->filter(fn ($summary) => $summary->exam !== null)->values();

        $recentViolations = Violation::with('exam:id,title,subject')
            ->where('user_id', $student->id)
            ->latest()
            ->limit(18)
            ->get();

        return view('admin.violations.student-overview', compact('student', 'examSummaries', 'recentViolations'));
    }

    public function student(Exam $exam, int $userId)
    {
        $student = User::withTrashed()->findOrFail($userId);

        $violations = Violation::with('exam:id,title,subject')
            ->where('exam_id', $exam->id)
            ->where('user_id', $student->id)
            ->latest()
            ->get();

        abort_if($violations->isEmpty(), 404);

        $reasonBreakdown = $violations
            ->groupBy('reason')
            ->map(fn ($group) => $group->count())
            ->sortDesc();

        return view('admin.violations.student', compact('exam', 'student', 'violations', 'reasonBreakdown'));
    }

    public function image(Violation $violation)
    {
        if (str_starts_with($violation->image_path, 'http://') || str_starts_with($violation->image_path, 'https://')) {
            return redirect()->away($violation->image_path);
        }

        abort_unless(Storage::disk('public')->exists($violation->image_path), 404);

        return response()->file(Storage::disk('public')->path($violation->image_path));
    }
}
