<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiGeneratedQuestion;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AiQuestionController extends Controller
{
    public function generate(Request $request, Exam $exam, GeminiAiService $geminiAiService): JsonResponse
    {
        $data = $request->validate([
            'subjects' => 'required|string|max:1000',
            'topics' => 'nullable|string|max:1000',
            'difficulty' => 'required|string|in:easy,medium,hard,mixed',
            'question_count' => 'required|integer|min:1|max:15',
        ]);

        $subjects = $this->splitList($data['subjects']);
        $topics = $this->splitList($data['topics'] ?? '') ?: [$exam->subject ?: 'General'];
        $cacheKey = 'ai:generated-questions:'.sha1(json_encode([
            'exam_id' => $exam->id,
            'exam_department' => $exam->department,
            'exam_semester' => $exam->semester,
            'subjects' => $subjects,
            'topics' => $topics,
            'difficulty' => $data['difficulty'],
            'count' => (int) $data['question_count'],
        ]));

        $examDepartment = $exam->department ? implode(', ', (array) $exam->department) : 'All Departments';
        $examSemester = $exam->semester ? implode(', ', (array) $exam->semester) : 'All Semesters';

        $questions = $geminiAiService->generateQuestions([
            'exam_title' => $exam->title,
            'department' => $examDepartment,
            'semester' => $examSemester,
            'subjects' => $subjects,
            'topics' => $topics,
            'difficulty' => $data['difficulty'],
            'question_count' => (int) $data['question_count'],
            'cache_key' => $cacheKey,
        ]);

        $records = collect($questions)->map(function (array $item) use ($exam, $cacheKey, $geminiAiService) {
            $options = $this->normalizeOptions(Arr::get($item, 'options', []));
            $correctAnswer = $this->normalizeOptionText((string) Arr::get($item, 'correct_answer', ''));

            if (count($options) !== 4 || ! in_array($correctAnswer, $options, true)) {
                return null;
            }

            return AiGeneratedQuestion::create([
                'exam_id' => $exam->id,
                'generated_by' => auth()->id(),
                'subject' => (string) Arr::get($item, 'subject', $exam->subject ?: 'General'),
                'difficulty' => (string) Arr::get($item, 'difficulty', 'mixed'),
                'topic' => (string) Arr::get($item, 'topic', 'General'),
                'question' => trim((string) Arr::get($item, 'question')),
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'explanation' => trim((string) Arr::get($item, 'explanation', '')),
                'status' => 'pending',
                'model' => $geminiAiService->model(),
                'cache_key' => $cacheKey,
                'raw_payload' => $item,
            ]);
        })->filter()->values();

        return response()->json([
            'message' => 'AI questions generated successfully.',
            'questions' => $records->map(fn (AiGeneratedQuestion $question) => $this->serializeGeneratedQuestion($question)),
        ]);
    }

    public function update(Request $request, Exam $exam, AiGeneratedQuestion $generatedQuestion): JsonResponse
    {
        abort_unless($generatedQuestion->exam_id === $exam->id, 404);

        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'difficulty' => 'required|string|in:easy,medium,hard,mixed',
            'topic' => 'nullable|string|max:255',
            'question' => 'required|string',
            'options' => 'required|array|size:4',
            'options.*' => 'required|string|max:500',
            'correct_answer' => 'required|string|max:500',
            'explanation' => 'nullable|string',
        ]);

        $options = $this->normalizeOptions($data['options']);
        $correctAnswer = $this->normalizeOptionText($data['correct_answer']);

        if (! in_array($correctAnswer, $options, true)) {
            return response()->json([
                'message' => 'Correct answer must match one of the options exactly.',
            ], 422);
        }

        $generatedQuestion->update([
            'subject' => $data['subject'],
            'difficulty' => $data['difficulty'],
            'topic' => $data['topic'],
            'question' => $data['question'],
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'explanation' => $data['explanation'],
            'status' => $generatedQuestion->status === 'rejected' ? 'pending' : $generatedQuestion->status,
        ]);

        return response()->json([
            'message' => 'AI draft updated.',
            'question' => $this->serializeGeneratedQuestion($generatedQuestion->fresh()),
        ]);
    }

    public function approve(Exam $exam, AiGeneratedQuestion $generatedQuestion): JsonResponse
    {
        abort_unless($generatedQuestion->exam_id === $exam->id, 404);

        if ($generatedQuestion->status === 'approved' && $generatedQuestion->question_id) {
            return response()->json([
                'message' => 'Question already approved.',
                'question' => $this->serializeGeneratedQuestion($generatedQuestion),
            ]);
        }

        DB::transaction(function () use ($generatedQuestion, $exam) {
            $cleanOptions = $this->normalizeOptions($generatedQuestion->options ?? []);
            $cleanCorrectAnswer = $this->normalizeOptionText($generatedQuestion->correct_answer);

            $question = Question::create([
                'exam_id' => $exam->id,
                'question_text' => $generatedQuestion->question,
                'topic' => $generatedQuestion->topic,
                'question_type' => 'mcq',
                'marks' => 1,
                'explanation' => $generatedQuestion->explanation,
            ]);

            foreach ($cleanOptions as $index => $option) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $option,
                    'is_correct' => trim($option) === trim($cleanCorrectAnswer),
                    'order_index' => $index,
                ]);
            }

            $generatedQuestion->update([
                'options' => $cleanOptions,
                'correct_answer' => $cleanCorrectAnswer,
                'status' => 'approved',
                'question_id' => $question->id,
                'approved_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'message' => 'AI draft approved and added to the exam.',
            'question' => $this->serializeGeneratedQuestion($generatedQuestion->fresh()),
        ]);
    }

    public function reject(Exam $exam, AiGeneratedQuestion $generatedQuestion): JsonResponse
    {
        abort_unless($generatedQuestion->exam_id === $exam->id, 404);

        $generatedQuestion->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'AI draft rejected.',
            'question' => $this->serializeGeneratedQuestion($generatedQuestion->fresh()),
        ]);
    }

    protected function splitList(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function serializeGeneratedQuestion(AiGeneratedQuestion $question): array
    {
        $cleanOptions = $this->normalizeOptions($question->options ?? []);
        $cleanCorrectAnswer = $this->normalizeOptionText($question->correct_answer);

        return [
            'id' => $question->id,
            'subject' => $question->subject,
            'difficulty' => $question->difficulty,
            'topic' => $question->topic,
            'question' => $question->question,
            'options' => $cleanOptions,
            'correct_answer' => $cleanCorrectAnswer,
            'explanation' => $question->explanation,
            'status' => $question->status,
            'question_id' => $question->question_id,
            'approved_url' => $question->question_id ? route('admin.questions.edit', $question->question_id) : null,
        ];
    }

    protected function normalizeOptions(array $options): array
    {
        return collect($options)
            ->map(fn ($option) => $this->normalizeOptionText((string) $option))
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizeOptionText(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/^\s*(?:[A-Za-z]|\d+)[\)\].:\-]\s*/', '', $value) ?? $value;
        $value = preg_replace('/^\s*[A-Za-z]\s+\)\s*/', '', $value) ?? $value;

        return trim($value);
    }
}
