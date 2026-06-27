<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function index()
    {
        $departments = config('academix.departments', []);
        $departmentSummaries = collect($departments)->map(function (string $departmentName) {
            $baseQuery = Exam::query();
            Exam::applyAcademicFieldFilter($baseQuery, 'department', [$departmentName], true);

            return [
                'name' => $departmentName,
                'total_exams' => (clone $baseQuery)->count(),
                'active_exams' => (clone $baseQuery)->where('status', 'published')->count(),
            ];
        });

        return view('admin.exams.index', compact('departmentSummaries'));
    }

    public function department(Request $request, string $department)
    {
        $departments = config('academix.departments', []);
        $semesters = array_reverse(config('academix.semesters', []));

        if (! in_array($department, $departments, true)) {
            abort(404);
        }

        $search = trim((string) $request->query('search', ''));
        $status = strtolower((string) $request->query('status', 'all'));

        $examsQuery = Exam::withCount('questions');
        Exam::applyAcademicFieldFilter($examsQuery, 'department', [$department], true);

        if ($search !== '') {
            $examsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if (in_array($status, ['draft', 'published'], true)) {
            $examsQuery->where('status', $status);
        } else {
            $status = 'all';
        }

        $exams = $examsQuery
            ->latest()
            ->get();

        $semesterSections = collect($semesters)->map(function (string $semester) use ($exams) {
            $semesterExams = $exams->filter(function ($exam) use ($semester) {
                return empty($exam->semester)
                    || (is_array($exam->semester) && in_array($semester, $exam->semester, true))
                    || $exam->semester === $semester
                    || $exam->semester === '';
            })->values();

            return [
                'name' => $semester,
                'exams' => $semesterExams,
            ];
        });

        return view('admin.exams.department', compact(
            'search',
            'status',
            'department',
            'semesterSections',
        ));
    }

    public function create()
    {
        return view('admin.exams.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateExam($request);

        $exam = Exam::create(array_merge(
            $this->buildExamPayload($validated, $request),
            [
                'created_by' => Auth::id(),
                'status' => 'draft',
            ]
        ));

        return redirect()
            ->route('admin.exams.edit', $exam)
            ->with('success', 'Exam created. Add questions now.');
    }

    public function edit(Exam $exam)
    {
        $exam->load(['questions.options' => function ($q) {
            $q->orderBy('id');
        }, 'aiGeneratedQuestions']);

        return view('admin.exams.edit', compact('exam'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $this->validateExam($request);

        $exam->update($this->buildExamPayload($validated, $request));

        return back()->with('success', 'Exam updated.');
    }

    public function show(Exam $exam)
    {
        $exam->load('questions.options');

        return view('admin.exams.show', compact('exam'));
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()
            ->route('admin.exams.index')
            ->with('success', 'Exam deleted.');
    }

    public function togglePublish(Exam $exam)
    {
        $exam->status = $exam->status === 'published'
            ? 'draft'
            : 'published';

        $exam->save();

        return back()->with(
            'success',
            'Exam status changed to ' . ucfirst($exam->status)
        );
    }

    protected function validateExam(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'pass_percentage' => 'required|numeric|min:0|max:100',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'department' => 'nullable|array',
            'department.*' => 'in:' . implode(',', config('academix.departments', [])),
            'semester' => 'nullable|array',
            'semester.*' => 'in:' . implode(',', config('academix.semesters', [])),
            'negative_enabled' => 'nullable|boolean',
            'negative_marking' => 'nullable|numeric|min:0',
            'proctoring_enabled' => 'nullable|boolean',
            'require_camera' => 'nullable|boolean',
            'require_microphone' => 'nullable|boolean',
            'detect_no_face' => 'nullable|boolean',
            'detect_multiple_faces' => 'nullable|boolean',
            'detect_talking' => 'nullable|boolean',
            'max_warnings' => 'nullable|integer|min:1|max:10',
            'pre_exam_countdown_seconds' => 'nullable|integer|min:0|max:60',
        ]);
    }

    protected function buildExamPayload(array $validated, Request $request): array
    {
        $proctoringEnabled = $request->boolean('proctoring_enabled');
        $requireCamera = $proctoringEnabled && $request->boolean('require_camera');
        $requireMicrophone = $proctoringEnabled && $request->boolean('require_microphone');

        return [
            'title' => $validated['title'],
            'subject' => $validated['subject'] ?? null,
            'department' => $validated['department'] ?? [],
            'description' => $validated['description'] ?? null,
            'duration_minutes' => $validated['duration_minutes'],
            'pass_percentage' => $validated['pass_percentage'],
            'semester' => $validated['semester'] ?? [],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'negative_enabled' => $request->boolean('negative_enabled'),
            'negative_marking' => $validated['negative_marking'] ?? 0,
            'proctoring_enabled' => $proctoringEnabled,
            'require_camera' => $requireCamera,
            'require_microphone' => $requireMicrophone,
            'detect_no_face' => $requireCamera && $request->boolean('detect_no_face'),
            'detect_multiple_faces' => $requireCamera && $request->boolean('detect_multiple_faces'),
            'detect_talking' => $requireMicrophone && $request->boolean('detect_talking'),
            'max_warnings' => $validated['max_warnings'] ?? 5,
            'pre_exam_countdown_seconds' => $validated['pre_exam_countdown_seconds'] ?? 10,
        ];
    }
}
