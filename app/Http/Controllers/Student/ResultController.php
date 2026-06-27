<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Response;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    // 📘 STUDENT EXAM HISTORY LIST
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $resultsQuery = Result::where('user_id', auth()->id())
            ->with('exam')
            ->latest();

        if ($search !== '') {
            $resultsQuery->whereHas('exam', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $results = $resultsQuery->get();

        return view('student.results.index', compact('results', 'search'));
    }

    // 📄 STUDENT FULL RESULT SHEET (DETAILED)
    public function show($resultId)
    {
        $result = Result::where('id', $resultId)
            ->where('user_id', auth()->id())
            ->with(['exam.questions.options', 'aiAnalysis', 'aiChatMessages'])
            ->firstOrFail();

        $exam = $result->exam;

        // 🔒 LOCK UNTIL EXAM ENDS
        $now = \Carbon\Carbon::now(config('app.timezone'));
        $examEnd = \Carbon\Carbon::parse($exam->end_time, config('app.timezone'));

        if ($now->lt($examEnd)) {
            return view('student.results.locked', compact('exam'));
        }

        // ✅ AFTER EXAM ENDS → SHOW DETAILS
        $responses = \App\Models\Response::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('question_id');

        $totalQuestions = $exam->questions->count();
        $attempted = $responses->whereNotNull('option_id')->count();
        $correct = $responses->where('is_correct', 1)->count();
        $wrong = $responses->where('is_correct', 0)->whereNotNull('option_id')->count();
        $notAnswered = $totalQuestions - $attempted;

        return view('student.results.show', compact(
            'result',
            'exam',
            'responses',
            'totalQuestions',
            'attempted',
            'correct',
            'wrong',
            'notAnswered'
        ));
    }
}
