<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiAiService
{
    public function generateQuestions(array $payload): array
    {
        $subjects = implode(', ', $payload['subjects']);
        $topics = implode(', ', $payload['topics']);

        $schema = [
            'type' => 'object',
            'properties' => [
                'questions' => [
                    'type' => 'array',
                    'minItems' => (int) $payload['question_count'],
                    'maxItems' => (int) $payload['question_count'],
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'subject' => ['type' => 'string'],
                            'difficulty' => ['type' => 'string'],
                            'topic' => ['type' => 'string'],
                            'question' => ['type' => 'string'],
                            'options' => [
                                'type' => 'array',
                                'minItems' => 4,
                                'maxItems' => 4,
                                'items' => ['type' => 'string'],
                            ],
                            'correct_answer' => ['type' => 'string'],
                            'explanation' => ['type' => 'string'],
                        ],
                        'required' => ['subject', 'difficulty', 'topic', 'question', 'options', 'correct_answer', 'explanation'],
                    ],
                ],
            ],
            'required' => ['questions'],
        ];

        $prompt = implode("\n", [
            'Generate high-quality multiple-choice exam questions.',
            'Return only valid JSON matching the provided schema.',
            'Requirements:',
            '- Keep questions academically sound and directly answerable.',
            '- Use exactly four options for each question.',
            '- Ensure one option matches correct_answer exactly.',
            '- Keep each explanation concise and practical.',
            '- Mix subjects and topics evenly when more than one is provided.',
            "- Subjects: {$subjects}",
            "- Topics: {$topics}",
            "- Difficulty: {$payload['difficulty']}",
            "- Number of questions: {$payload['question_count']}",
            '- Exam context:',
            '- Exam title: '.($payload['exam_title'] ?? 'N/A'),
            '- Department scope: '.($payload['department'] ?? 'All Departments'),
            '- Semester scope: '.($payload['semester'] ?? 'All Semesters'),
            '- Audience: undergraduate online exam students.',
            '- Avoid duplicate questions or trick wording.',
        ]);

        $response = $this->generateStructuredJson(
            cacheKey: $payload['cache_key'],
            prompt: $prompt,
            schema: $schema,
            ttlMinutes: (int) config('services.gemini.cache_minutes', 720),
        );

        return Arr::get($response, 'questions', []);
    }

    public function generateAnalyticsInsights(array $payload): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'headline' => ['type' => 'string'],
                'summary' => ['type' => 'string'],
                'insights' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 4,
                    'items' => ['type' => 'string'],
                ],
                'actions' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 3,
                    'items' => ['type' => 'string'],
                ],
            ],
            'required' => ['headline', 'summary', 'insights', 'actions'],
        ];

        $prompt = implode("\n", [
            'You are an academic analytics assistant for an online exam platform.',
            'Review the metrics and produce concise admin-facing insights.',
            'Respect the selected department/semester scope when interpreting the metrics.',
            'Identify weak topics, trends, and practical next steps.',
            'Keep insights evidence-based, not generic.',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);

        return $this->generateStructuredJson(
            cacheKey: $payload['cache_key'],
            prompt: $prompt,
            schema: $schema,
            ttlMinutes: (int) config('services.gemini.analytics_cache_minutes', 60),
        );
    }

    public function generateResultAnalysis(array $payload): array
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'overview' => ['type' => 'string'],
                'strengths' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 4,
                    'items' => ['type' => 'string'],
                ],
                'weaknesses' => [
                    'type' => 'array',
                    'minItems' => 2,
                    'maxItems' => 4,
                    'items' => ['type' => 'string'],
                ],
                'suggestions' => [
                    'type' => 'array',
                    'minItems' => 3,
                    'maxItems' => 5,
                    'items' => ['type' => 'string'],
                ],
                'topic_breakdown' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'topic' => ['type' => 'string'],
                            'status' => ['type' => 'string'],
                            'detail' => ['type' => 'string'],
                        ],
                        'required' => ['topic', 'status', 'detail'],
                    ],
                ],
            ],
            'required' => ['overview', 'strengths', 'weaknesses', 'suggestions', 'topic_breakdown'],
        ];

        $prompt = implode("\n", [
            'You are a supportive exam coach.',
            'Analyze this student result deeply but keep the tone constructive.',
            'Respect the student department/semester and exam scope provided in the payload.',
            'Use the question-level context to explain patterns in performance.',
            'Do not mention any subject, theory, formula, or example unless it appears in the provided result data.',
            'If a topic label is generic like "Question 3", use the question text itself for context instead of inventing a domain.',
            'Do not mention data you cannot infer from the provided result.',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);

        return $this->generateStructuredJson(
            cacheKey: $payload['cache_key'],
            prompt: $prompt,
            schema: $schema,
            ttlMinutes: (int) config('services.gemini.cache_minutes', 720),
        );
    }

    public function answerResultDoubt(array $payload): string
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'reply' => ['type' => 'string'],
            ],
            'required' => ['reply'],
        ];

        $prompt = implode("\n", [
            'You are answering a student question about a completed exam result.',
            'Be clear, accurate, and educational.',
            'Keep explanations aligned to the student department/semester and exam scope in the payload.',
            'If the student answer was wrong, explain why using the provided context.',
            'Do not invent facts outside the supplied exam/result data.',
            json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);

        $response = $this->generateStructuredJson(
            cacheKey: $payload['cache_key'],
            prompt: $prompt,
            schema: $schema,
            ttlMinutes: 240,
        );

        return trim((string) Arr::get($response, 'reply', ''));
    }

    public function model(): ?string
    {
        return config('services.gemini.model');
    }

    protected function generateStructuredJson(string $cacheKey, string $prompt, array $schema, int $ttlMinutes): array
    {
        $apiKey = config('services.gemini.api_key');
        $model = config('services.gemini.model');

        if (! $apiKey || ! $model) {
            throw new RuntimeException('Gemini API is not configured. Add GEMINI_API_KEY and GEMINI_MODEL to your environment.');
        }

        return Cache::remember($cacheKey, now()->addMinutes($ttlMinutes), function () use ($apiKey, $model, $prompt, $schema) {
            $response = Http::timeout(90)
                ->acceptJson()
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [[
                        'role' => 'user',
                        'parts' => [[
                            'text' => $prompt,
                        ]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'responseMimeType' => 'application/json',
                        'responseSchema' => $schema,
                    ],
                ]);

            if ($response->failed()) {
                throw new RuntimeException('Gemini request failed: '.$response->body());
            }

            $jsonText = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (! is_string($jsonText) || trim($jsonText) === '') {
                throw new RuntimeException('Gemini returned an empty response.');
            }

            $decoded = json_decode($jsonText, true);

            if (! is_array($decoded)) {
                throw new RuntimeException('Gemini returned invalid JSON.');
            }

            return $decoded;
        });
    }
}
