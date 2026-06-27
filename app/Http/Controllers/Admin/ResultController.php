<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Response;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // 📌 Show completed exams list
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $selectedDepartments = $this->normalizeMultiSelectFilter(
            (array) $request->query('department', []),
            (array) config('academix.departments', [])
        );
        $selectedSemesters = $this->normalizeMultiSelectFilter(
            (array) $request->query('semester', []),
            (array) config('academix.semesters', [])
        );

        $examsQuery = Exam::whereHas('results')
            ->withCount('results')
            ->latest();

        if ($search !== '') {
            $examsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if (count($selectedDepartments) > 0) {
            Exam::applyAcademicFieldFilter($examsQuery, 'department', $selectedDepartments);
        }

        if (count($selectedSemesters) > 0) {
            Exam::applyAcademicFieldFilter($examsQuery, 'semester', $selectedSemesters);
        }

        $exams = $examsQuery->get();

        return view('admin.results.exams', compact('exams', 'search', 'selectedDepartments', 'selectedSemesters'));
    }

    // 📌 Show students who attempted one exam
    public function show(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $filter = strtolower((string) $request->query('filter', 'all'));
        $department = trim((string) $request->query('department', ''));
        $semester = trim((string) $request->query('semester', ''));

        $resultsQuery = Result::with('user')
            ->where('exam_id', $exam->id)
            ->whereHas('user', function ($query) use ($department, $semester) {
                if ($department !== '') {
                    $query->where('department', $department);
                }

                if ($semester !== '') {
                    $query->where('semester', $semester);
                }
            });

        switch ($filter) {
            case 'highest':
                $resultsQuery->orderByDesc('obtained_marks');
                break;
            case 'lowest':
                $resultsQuery->orderBy('obtained_marks');
                break;
            case 'pass':
                $resultsQuery->where('status', 'Pass')
                    ->orderByDesc('obtained_marks');
                break;
            case 'fail':
                $resultsQuery->where('status', 'Fail')
                    ->orderByDesc('obtained_marks');
                break;
            default:
                $filter = 'all';
                $resultsQuery->orderByDesc('obtained_marks');
                break;
        }

        $results = $resultsQuery->get();

        return view('admin.results.students', compact('exam', 'results', 'filter', 'department', 'semester'));
    }

    // 📌 Full answer sheet for one student result
    public function sheet($resultId)
    {
        $result = Result::with(['exam.questions.options', 'user'])->findOrFail($resultId);

        $responses = Response::with('option')
            ->where('exam_id', $result->exam_id)
            ->where('user_id', $result->user_id)
            ->get()
            ->keyBy('question_id');

        $exam = $result->exam;
        $questions = $exam->questions;

        $totalQuestions = $questions->count();
        $attempted = $responses->whereNotNull('option_id')->count();
        $notAnswered = max(0, $totalQuestions - $attempted);

        return view('admin.results.sheet', compact(
            'result',
            'exam',
            'questions',
            'responses',
            'totalQuestions',
            'attempted',
            'notAnswered'
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
