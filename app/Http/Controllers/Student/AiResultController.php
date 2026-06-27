<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\AiResultAnalysis;
use App\Models\Result;
use App\Services\GeminiAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiResultController extends Controller
{
    public function analyze(Result $result, GeminiAiService $geminiAiService): JsonResponse
    {
        abort_unless($result->user_id === auth()->id(), 404);

        $result->loadMissing(['exam.questions.options', 'aiAnalysis', 'user']);
        $payload = $this->buildResultContext($result);
        $hash = sha1(json_encode([
            'version' => 2,
            'payload' => $payload,
        ]));

        if ($result->aiAnalysis && $result->aiAnalysis->prompt_hash === $hash) {
            return response()->json([
                'message' => 'Cached AI analysis loaded.',
                'analysis' => $result->aiAnalysis->analysis,
            ]);
        }

        $analysis = $geminiAiService->generateResultAnalysis([
            ...$payload,
            'cache_key' => 'ai:result-analysis:'.$result->id.':'.$hash,
        ]);

        AiResultAnalysis::updateOrCreate(
            [
                'result_id' => $result->id,
                'user_id' => $result->user_id,
            ],
            [
                'model' => $geminiAiService->model(),
                'prompt_hash' => $hash,
                'analysis' => $analysis,
            ]
        );

        return response()->json([
            'message' => 'AI analysis generated.',
            'analysis' => $analysis,
        ]);
    }

    public function chat(Request $request, Result $result, GeminiAiService $geminiAiService): JsonResponse
    {
        abort_unless($result->user_id === auth()->id(), 404);

        $data = $request->validate([
            'message' => 'required|string|max:1200',
        ]);

        $result->loadMissing(['exam.questions.options', 'aiChatMessages', 'user']);
        $history = $result->aiChatMessages()
            ->latest('id')
            ->limit(8)
            ->get()
            ->reverse()
            ->map(fn (AiChatMessage $message) => [
                'role' => $message->role,
                'message' => $message->message,
            ])
            ->values()
            ->all();

        $payload = [
            ...$this->buildResultContext($result),
            'conversation' => $history,
            'student_question' => $data['message'],
            'cache_key' => 'ai:result-chat:'.sha1(json_encode([
                'result' => $result->id,
                'history' => $history,
                'question' => $data['message'],
            ])),
        ];

        $reply = $geminiAiService->answerResultDoubt($payload);

        $userMessage = AiChatMessage::create([
            'result_id' => $result->id,
            'user_id' => $result->user_id,
            'role' => 'user',
            'message' => $data['message'],
        ]);

        $assistantMessage = AiChatMessage::create([
            'result_id' => $result->id,
            'user_id' => $result->user_id,
            'role' => 'assistant',
            'message' => $reply,
            'meta' => ['model' => $geminiAiService->model()],
        ]);

        return response()->json([
            'message' => 'AI reply generated.',
            'messages' => [
                [
                    'id' => $userMessage->id,
                    'role' => $userMessage->role,
                    'message' => $userMessage->message,
                ],
                [
                    'id' => $assistantMessage->id,
                    'role' => $assistantMessage->role,
                    'message' => $assistantMessage->message,
                ],
            ],
        ]);
    }

    protected function buildResultContext(Result $result): array
    {
        $responses = $result->responses()->with(['question.options', 'option'])->get()->keyBy('question_id');

        return [
            'exam' => [
                'title' => $result->exam->title,
                'subject' => $result->exam->subject,
                'department' => $result->exam->department ?: 'All Departments',
                'semester' => $result->exam->semester ?: 'All Semesters',
                'pass_percentage' => $result->exam->pass_percentage,
            ],
            'student' => [
                'department' => $result->user?->department ?: 'Not Set',
                'semester' => $result->user?->semester ?: 'Not Set',
            ],
            'result' => [
                'score' => $result->obtained_marks,
                'total_marks' => $result->total_marks,
                'percentage' => round((float) $result->percentage, 2),
                'status' => $result->status,
                'correct' => $result->correct,
                'wrong' => $result->wrong,
                'not_attempted' => $result->not_attempted,
            ],
            'questions' => $result->exam->questions->values()->map(function ($question, $index) use ($responses, $result) {
                $response = $responses->get($question->id);
                $correctOption = $question->options->firstWhere('is_correct', true);
                $topic = trim((string) ($question->topic ?? ''));

                return [
                    'question' => $question->question_text,
                    'topic' => $topic !== '' ? $topic : 'Question '.($index + 1),
                    'student_answer' => $response?->option?->option_text,
                    'correct_answer' => $correctOption?->option_text,
                    'is_correct' => (bool) $response?->is_correct,
                    'explanation' => $question->explanation,
                    'options' => $question->options->pluck('option_text')->values()->all(),
                ];
            })->values()->all(),
        ];
    }
}
