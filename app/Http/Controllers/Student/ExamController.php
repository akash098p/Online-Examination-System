<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Exam;
use App\Models\Response as Resp;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $semester = $user->semester;
        $ownDepartment = $user->department;
        $departments = config('academix.departments', []);
        $selectedDepartment = $request->query('department');

        if (! in_array($selectedDepartment, $departments, true)) {
            $selectedDepartment = $ownDepartment;
        }

        $exams = Exam::where('status', 'published')
            ->when(! empty($semester), fn ($query) => Exam::applyAcademicFieldFilter($query, 'semester', [$semester], true))
            ->when(! empty($selectedDepartment), fn ($query) => Exam::applyAcademicFieldFilter($query, 'department', [$selectedDepartment], true))
            ->withCount('questions')
            ->with(['questions:id,exam_id,marks'])
            ->orderByRaw('CASE WHEN department IS NULL THEN 0 ELSE 1 END')
            ->latest()
            ->get();

        return view('student.exams.index', compact('exams', 'departments', 'selectedDepartment', 'ownDepartment'));
    }

    public function start($examId)
    {
        $exam = Exam::with('questions.options')->findOrFail($examId);
        $user = auth()->user();

        if (! $this->studentCanAttemptExam($exam, $user)) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'You can only attempt exams from your own department and semester.');
        }

        $now       = Carbon::now(config('app.timezone'));
        $startTime = Carbon::parse($exam->start_time, config('app.timezone'));
        $endTime   = Carbon::parse($exam->end_time, config('app.timezone'));

        if ($now->lt($startTime)) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'Exam has not started yet.');
        }

        if ($now->gt($endTime)) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'Exam time is over.');
        }

        if (Result::where('exam_id', $examId)->where('user_id', auth()->id())->exists()) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'You have already attempted this exam.');
        }

        $questions = $exam->questions()->with('options')->get();

        session()->put("exam_attempt_{$examId}", [
            'attempt_id' => uniqid('attempt_' . auth()->id() . '_'),
            'started_at' => Carbon::now(),
        ]);

        return view('student.exams.start', compact('exam', 'questions'));
    }

    public function submit(Request $request, $examId)
    {
        $userId = auth()->id();
        $user = auth()->user();

        if (Result::where('exam_id', $examId)->where('user_id', $userId)->exists()) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'You have already attempted this exam.');
        }

        $exam = Exam::with('questions.options')->findOrFail($examId);

        if (! $this->studentCanAttemptExam($exam, $user)) {
            return redirect()->route('student.exams.index', ['department' => $user->department])
                ->with('error', 'You can only submit exams from your own department and semester.');
        }

        $totalQuestions = $exam->questions->count();
        $totalMarks     = 0;
        $obtainedMarks  = 0;

        $correct = 0;
        $wrong = 0;
        $notAttempted = 0;

        foreach ($exam->questions as $question) {

            $qid = $question->id;
            $selectedOptionId = $request->input("answers.$qid");

            $correctOptionId = $question->options
                ->where('is_correct', 1)
                ->pluck('id')
                ->first();

            $marks = (int) ($question->marks ?? 1);
            $totalMarks += $marks;

            // ❌ NOT ATTEMPTED
            if (is_null($selectedOptionId)) {

                $notAttempted++;

                Resp::create([
                    'exam_id' => $examId,
                    'user_id' => $userId,
                    'question_id' => $qid,
                    'option_id' => null,
                    'is_correct' => 0,
                    'marks_obtained' => 0,
                ]);

                continue;
            }

            // ✅ CORRECT
            if ((int)$selectedOptionId === (int)$correctOptionId) {

                $correct++;
                $obtainedMarks += $marks;

                Resp::create([
                    'exam_id' => $examId,
                    'user_id' => $userId,
                    'question_id' => $qid,
                    'option_id' => $selectedOptionId,
                    'is_correct' => 1,
                    'marks_obtained' => $marks,
                ]);
            }
            // ❌ WRONG
            else {

                $wrong++;

                // ✅ NEGATIVE MARKING LOGIC (ADDED)
                if ($exam->negative_enabled) {
                    $obtainedMarks -= (float) $exam->negative_marking;
                }

                Resp::create([
                    'exam_id' => $examId,
                    'user_id' => $userId,
                    'question_id' => $qid,
                    'option_id' => $selectedOptionId,
                    'is_correct' => 0,
                    'marks_obtained' => 0,
                ]);
            }
        }

        // Prevent negative total score
        if ($obtainedMarks < 0) {
            $obtainedMarks = 0;
        }

        $percentage = $totalMarks > 0
            ? ($obtainedMarks / $totalMarks) * 100
            : 0;
        $passPercentage = (float) ($exam->pass_percentage ?? 40);

        Result::create([
            'exam_id'         => $examId,
            'user_id'         => $userId,
            'total_questions' => $totalQuestions,
            'correct'         => $correct,
            'wrong'           => $wrong,
            'not_attempted'   => $notAttempted,
            'total_marks'     => $totalMarks,
            'obtained_marks'  => $obtainedMarks,
            'percentage'      => $percentage,
            'status'          => $percentage >= $passPercentage ? 'Pass' : 'Fail',
        ]);

        session()->forget("exam_attempt_{$examId}");
        session()->forget($this->draftSessionKey($examId, $userId));

        return redirect()->route('student.exams.index', ['department' => $user->department])
            ->with('success', 'Exam submitted successfully.');
    }

    public function result($examId)
    {
        $userId = auth()->id();

        $result = Result::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $exam = Exam::with('questions')->findOrFail($examId);

        return view('student.exams.result', [
            'exam'           => $exam,
            'totalMarks'     => $result->total_marks,
            'obtainedMarks'  => $result->obtained_marks,
            'percentage'     => $result->percentage,
        ]);
    }

    public function autosave(Request $request, $examId)
    {
        $exam = Exam::findOrFail($examId);
        $user = auth()->user();

        if (! $this->studentCanAttemptExam($exam, $user)) {
            return response()->json(['message' => 'Unauthorized exam access.'], 403);
        }

        $data = $request->validate([
            'attempt_id' => 'nullable|string|max:100',
            'answers' => 'nullable|array',
            'flagged' => 'nullable|array',
        ]);

        session()->put($this->draftSessionKey($examId, $user->id), [
            'attempt_id' => $data['attempt_id'] ?? null,
            'answers' => $data['answers'] ?? [],
            'flagged' => $data['flagged'] ?? [],
            'saved_at' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Saved.']);
    }

    public function loadSaved($examId)
    {
        $exam = Exam::findOrFail($examId);
        $user = auth()->user();

        if (! $this->studentCanAttemptExam($exam, $user)) {
            return response()->json([
                'answers' => [],
                'flagged' => [],
            ], 403);
        }

        $draft = session()->get($this->draftSessionKey($examId, $user->id), []);

        return response()->json([
            'answers' => $draft['answers'] ?? [],
            'flagged' => $draft['flagged'] ?? [],
        ]);
    }

    protected function studentCanAttemptExam(Exam $exam, $user): bool
    {
        $semesterAllowed = empty($exam->semester) || in_array($user->semester, (array) $exam->semester, true);
        $departmentAllowed = empty($exam->department) || in_array($user->department, (array) $exam->department, true);

        return $semesterAllowed && $departmentAllowed;
    }

    protected function draftSessionKey($examId, $userId): string
    {
        return "exam_draft_{$examId}_{$userId}";
    }
}
