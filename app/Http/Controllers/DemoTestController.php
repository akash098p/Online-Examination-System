<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DemoTestController extends Controller
{
    public function index()
    {
        $tests = collect($this->getDemoTests())
            ->map(fn ($test) => Arr::only($test, ['slug', 'title', 'description', 'duration_minutes']))
            ->values();

        return view('demo.index', compact('tests'));
    }

    public function start(string $slug)
    {
        $test = $this->findTest($slug);

        if (! $test) {
            abort(404);
        }

        return view('demo.start', compact('test'));
    }

    public function submit(Request $request, string $slug)
    {
        $test = $this->findTest($slug);

        if (! $test) {
            abort(404);
        }

        $answers = $request->input('answers', []);
        $questions = $test['questions'];

        $resultRows = [];
        $correct = 0;
        $wrong = 0;
        $notAttempted = 0;

        foreach ($questions as $question) {
            $selected = $answers[$question['id']] ?? null;
            $isAttempted = ! is_null($selected) && $selected !== '';
            $isCorrect = $isAttempted && ((string) $selected === (string) $question['correct_option']);
            $optionMap = collect($question['options'])->pluck('text', 'id');

            if (! $isAttempted) {
                $notAttempted++;
            } elseif ($isCorrect) {
                $correct++;
            } else {
                $wrong++;
            }

            $resultRows[] = [
                'question_id' => $question['id'],
                'question_text' => $question['question_text'],
                'selected' => $selected,
                'selected_text' => $isAttempted ? ($optionMap[$selected] ?? 'Unknown') : null,
                'correct_option' => $question['correct_option'],
                'correct_text' => $optionMap[$question['correct_option']] ?? 'Unknown',
                'is_attempted' => $isAttempted,
                'is_correct' => $isCorrect,
            ];
        }

        $totalQuestions = count($questions);
        $score = $correct;
        $percentage = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100, 2) : 0;

        $payload = [
            'test' => Arr::only($test, ['slug', 'title', 'description', 'duration_minutes']),
            'summary' => [
                'total_questions' => $totalQuestions,
                'correct' => $correct,
                'wrong' => $wrong,
                'not_attempted' => $notAttempted,
                'score' => $score,
                'percentage' => $percentage,
            ],
            'rows' => $resultRows,
        ];

        session()->put("demo_results.$slug", $payload);

        return redirect()->route('demo.result', $slug);
    }

    public function result(string $slug)
    {
        $result = session("demo_results.$slug");

        if (! $result) {
            return redirect()->route('demo.start', $slug)
                ->with('error', 'Please submit the test first.');
        }

        return view('demo.result', compact('result'));
    }

    private function findTest(string $slug): ?array
    {
        return $this->getDemoTests()[$slug] ?? null;
    }

    private function getDemoTests(): array
    {
        return [
            'aptitude-basics' => [
                'slug' => 'aptitude-basics',
                'title' => 'Test 1: Aptitude Basics',
                'description' => 'Quick aptitude test with simple math and reasoning questions.',
                'duration_minutes' => 10,
                'questions' => [
                    [
                        'id' => 1,
                        'question_text' => 'What is 15% of 200?',
                        'options' => [
                            ['id' => 'a', 'text' => '20'],
                            ['id' => 'b', 'text' => '25'],
                            ['id' => 'c', 'text' => '30'],
                            ['id' => 'd', 'text' => '40'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 2,
                        'question_text' => 'If a train travels 60 km in 1 hour, how far in 3 hours?',
                        'options' => [
                            ['id' => 'a', 'text' => '120 km'],
                            ['id' => 'b', 'text' => '180 km'],
                            ['id' => 'c', 'text' => '200 km'],
                            ['id' => 'd', 'text' => '240 km'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 3,
                        'question_text' => 'Find the next number in the series: 2, 4, 8, 16, ?',
                        'options' => [
                            ['id' => 'a', 'text' => '18'],
                            ['id' => 'b', 'text' => '24'],
                            ['id' => 'c', 'text' => '30'],
                            ['id' => 'd', 'text' => '32'],
                        ],
                        'correct_option' => 'd',
                    ],
                    [
                        'id' => 4,
                        'question_text' => 'A bag contains 5 red and 5 blue balls. Probability of picking red first?',
                        'options' => [
                            ['id' => 'a', 'text' => '1/10'],
                            ['id' => 'b', 'text' => '1/2'],
                            ['id' => 'c', 'text' => '2/5'],
                            ['id' => 'd', 'text' => '3/5'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 5,
                        'question_text' => 'Simplify: 9 + 3 x 2',
                        'options' => [
                            ['id' => 'a', 'text' => '24'],
                            ['id' => 'b', 'text' => '18'],
                            ['id' => 'c', 'text' => '15'],
                            ['id' => 'd', 'text' => '12'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 6,
                        'question_text' => 'What is 25% of 80?',
                        'options' => [
                            ['id' => 'a', 'text' => '10'],
                            ['id' => 'b', 'text' => '15'],
                            ['id' => 'c', 'text' => '20'],
                            ['id' => 'd', 'text' => '25'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 7,
                        'question_text' => 'Find the missing number: 3, 6, 12, 24, ?',
                        'options' => [
                            ['id' => 'a', 'text' => '30'],
                            ['id' => 'b', 'text' => '36'],
                            ['id' => 'c', 'text' => '48'],
                            ['id' => 'd', 'text' => '54'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 8,
                        'question_text' => 'A man walks 4 km north and 3 km east. Distance from start?',
                        'options' => [
                            ['id' => 'a', 'text' => '5 km'],
                            ['id' => 'b', 'text' => '6 km'],
                            ['id' => 'c', 'text' => '7 km'],
                            ['id' => 'd', 'text' => '8 km'],
                        ],
                        'correct_option' => 'a',
                    ],
                    [
                        'id' => 9,
                        'question_text' => 'If 5 pens cost 50, what is cost of 8 pens?',
                        'options' => [
                            ['id' => 'a', 'text' => '70'],
                            ['id' => 'b', 'text' => '75'],
                            ['id' => 'c', 'text' => '80'],
                            ['id' => 'd', 'text' => '85'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 10,
                        'question_text' => 'Average of 15, 25, 35 is:',
                        'options' => [
                            ['id' => 'a', 'text' => '15'],
                            ['id' => 'b', 'text' => '20'],
                            ['id' => 'c', 'text' => '25'],
                            ['id' => 'd', 'text' => '30'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 11,
                        'question_text' => 'What is under root of 625?',
                        'options' => [
                            ['id' => 'a', 'text' => '35'],
                            ['id' => 'b', 'text' => '15'],
                            ['id' => 'c', 'text' => '25'],
                            ['id' => 'd', 'text' => '55'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 12,
                        'question_text' => 'Simple interest on 1000 at 10% for 2 years is:',
                        'options' => [
                            ['id' => 'a', 'text' => '100'],
                            ['id' => 'b', 'text' => '150'],
                            ['id' => 'c', 'text' => '200'],
                            ['id' => 'd', 'text' => '250'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 13,
                        'question_text' => 'If ratio of boys:girls is 3:2 and total is 25, girls are:',
                        'options' => [
                            ['id' => 'a', 'text' => '10'],
                            ['id' => 'b', 'text' => '12'],
                            ['id' => 'c', 'text' => '15'],
                            ['id' => 'd', 'text' => '8'],
                        ],
                        'correct_option' => 'a',
                    ],
                    [
                        'id' => 14,
                        'question_text' => 'What is 3/4 of 200?',
                        'options' => [
                            ['id' => 'a', 'text' => '100'],
                            ['id' => 'b', 'text' => '120'],
                            ['id' => 'c', 'text' => '150'],
                            ['id' => 'd', 'text' => '175'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 15,
                        'question_text' => 'Find odd one out: 2, 3, 5, 9, 11',
                        'options' => [
                            ['id' => 'a', 'text' => '2'],
                            ['id' => 'b', 'text' => '5'],
                            ['id' => 'c', 'text' => '9'],
                            ['id' => 'd', 'text' => '11'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 16,
                        'question_text' => 'A clock shows 3:00. Angle between hands is:',
                        'options' => [
                            ['id' => 'a', 'text' => '60 deg'],
                            ['id' => 'b', 'text' => '75 deg'],
                            ['id' => 'c', 'text' => '90 deg'],
                            ['id' => 'd', 'text' => '120 deg'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 17,
                        'question_text' => 'If x²+x-6 = 0, x = ?',
                        'options' => [
                            ['id' => 'a', 'text' => '2,3'],
                            ['id' => 'b', 'text' => '-2,3'],
                            ['id' => 'c', 'text' => '2,-3'],
                            ['id' => 'd', 'text' => '-2,-3'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 18,
                        'question_text' => 'Perimeter of a square with side 9 is:',
                        'options' => [
                            ['id' => 'a', 'text' => '18'],
                            ['id' => 'b', 'text' => '27'],
                            ['id' => 'c', 'text' => '36'],
                            ['id' => 'd', 'text' => '81'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 19,
                        'question_text' => 'Convert 0.75 to percentage:',
                        'options' => [
                            ['id' => 'a', 'text' => '7.5%'],
                            ['id' => 'b', 'text' => '75%'],
                            ['id' => 'c', 'text' => '0.75%'],
                            ['id' => 'd', 'text' => '750%'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 20,
                        'question_text' => 'If 2x = 18, x = ?',
                        'options' => [
                            ['id' => 'a', 'text' => '7'],
                            ['id' => 'b', 'text' => '8'],
                            ['id' => 'c', 'text' => '9'],
                            ['id' => 'd', 'text' => '10'],
                        ],
                        'correct_option' => 'c',
                    ],
                ],
            ],
            'general-knowledge' => [
                'slug' => 'general-knowledge',
                'title' => 'Test 2: General Knowledge',
                'description' => 'Sample objective questions across history, geography, science and current basics.',
                'duration_minutes' => 10,
                'questions' => [
                    [
                        'id' => 1,
                        'question_text' => 'Which planet is known as the Red Planet?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Earth'],
                            ['id' => 'b', 'text' => 'Mars'],
                            ['id' => 'c', 'text' => 'Jupiter'],
                            ['id' => 'd', 'text' => 'Venus'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 2,
                        'question_text' => 'Who administers the oath of office to the President of India?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Prime Minister'],
                            ['id' => 'b', 'text' => 'Chief Justice of India'],
                            ['id' => 'c', 'text' => 'Vice President'],
                            ['id' => 'd', 'text' => 'Speaker of Lok Sabha'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 3,
                        'question_text' => 'Water boils at what temperature at sea level?',
                        'options' => [
                            ['id' => 'a', 'text' => '90 deg C'],
                            ['id' => 'b', 'text' => '95 deg C'],
                            ['id' => 'c', 'text' => '100 deg C'],
                            ['id' => 'd', 'text' => '110 deg C'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 4,
                        'question_text' => 'Which gas do plants absorb from the atmosphere?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Oxygen'],
                            ['id' => 'b', 'text' => 'Hydrogen'],
                            ['id' => 'c', 'text' => 'Carbon Dioxide'],
                            ['id' => 'd', 'text' => 'Nitrogen'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 5,
                        'question_text' => 'Who wrote the novel "The Guide"?',
                        'options' => [
                            ['id' => 'a', 'text' => 'R. K. Narayan'],
                            ['id' => 'b', 'text' => 'Rabindranath Tagore'],
                            ['id' => 'c', 'text' => 'Mulk Raj Anand'],
                            ['id' => 'd', 'text' => 'Arundhati Roy'],
                        ],
                        'correct_option' => 'a',
                    ],
                    [
                        'id' => 6,
                        'question_text' => 'Largest ocean on Earth is:',
                        'options' => [
                            ['id' => 'a', 'text' => 'Atlantic Ocean'],
                            ['id' => 'b', 'text' => 'Indian Ocean'],
                            ['id' => 'c', 'text' => 'Arctic Ocean'],
                            ['id' => 'd', 'text' => 'Pacific Ocean'],
                        ],
                        'correct_option' => 'd',
                    ],
                    [
                        'id' => 7,
                        'question_text' => 'Which countries will co-host the ICC Men\'s T20 World Cup 2028?',
                        'options' => [
                            ['id' => 'a', 'text' => 'India and Sri Lanka'],
                            ['id' => 'b', 'text' => 'Australia and New Zealand'],
                            ['id' => 'c', 'text' => 'South Africa and USA'],
                            ['id' => 'd', 'text' => 'England and Wales'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 8,
                        'question_text' => 'Which wind system is responsible for the Indian monsoon?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Trade winds'],
                            ['id' => 'b', 'text' => 'Westerlies'],
                            ['id' => 'c', 'text' => 'Monsoon winds'],
                            ['id' => 'd', 'text' => 'Polar winds'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 9,
                        'question_text' => 'Who is known as the Father of Computers?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Alan Turing'],
                            ['id' => 'b', 'text' => 'Charles Babbage'],
                            ['id' => 'c', 'text' => 'Bill Gates'],
                            ['id' => 'd', 'text' => 'Steve Jobs'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 10,
                        'question_text' => 'Which vitamin is produced when skin is exposed to sunlight?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Vitamin A'],
                            ['id' => 'b', 'text' => 'Vitamin B12'],
                            ['id' => 'c', 'text' => 'Vitamin C'],
                            ['id' => 'd', 'text' => 'Vitamin D'],
                        ],
                        'correct_option' => 'd',
                    ],
                    [
                        'id' => 11,
                        'question_text' => 'How many continents are there?',
                        'options' => [
                            ['id' => 'a', 'text' => '5'],
                            ['id' => 'b', 'text' => '6'],
                            ['id' => 'c', 'text' => '7'],
                            ['id' => 'd', 'text' => '8'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 12,
                        'question_text' => 'Which is the smallest prime number?',
                        'options' => [
                            ['id' => 'a', 'text' => '0'],
                            ['id' => 'b', 'text' => '1'],
                            ['id' => 'c', 'text' => '2'],
                            ['id' => 'd', 'text' => '3'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 13,
                        'question_text' => 'Which number is known as Hardy-Ramanujan number?',
                        'options' => [
                            ['id' => 'a', 'text' => '1001'],
                            ['id' => 'b', 'text' => '1729'],
                            ['id' => 'c', 'text' => '999'],
                            ['id' => 'd', 'text' => '1331'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 14,
                        'question_text' => 'Which blood group is known as universal donor?',
                        'options' => [
                            ['id' => 'a', 'text' => 'AB+'],
                            ['id' => 'b', 'text' => 'O-'],
                            ['id' => 'c', 'text' => 'A+'],
                            ['id' => 'd', 'text' => 'B-'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 15,
                        'question_text' => 'What is the chemical symbol of gold?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Ag'],
                            ['id' => 'b', 'text' => 'Gd'],
                            ['id' => 'c', 'text' => 'Au'],
                            ['id' => 'd', 'text' => 'Go'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 16,
                        'question_text' => 'Which institution controls monetary policy in India?',
                        'options' => [
                            ['id' => 'a', 'text' => 'SEBI'],
                            ['id' => 'b', 'text' => 'RBI'],
                            ['id' => 'c', 'text' => 'IMF'],
                            ['id' => 'd', 'text' => 'Finance Ministry'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 17,
                        'question_text' => 'The process by which plants make food is called:',
                        'options' => [
                            ['id' => 'a', 'text' => 'Respiration'],
                            ['id' => 'b', 'text' => 'Transpiration'],
                            ['id' => 'c', 'text' => 'Photosynthesis'],
                            ['id' => 'd', 'text' => 'Digestion'],
                        ],
                        'correct_option' => 'c',
                    ],
                    [
                        'id' => 18,
                        'question_text' => 'Which instrument is used to measure temperature?',
                        'options' => [
                            ['id' => 'a', 'text' => 'Barometer'],
                            ['id' => 'b', 'text' => 'Thermometer'],
                            ['id' => 'c', 'text' => 'Hygrometer'],
                            ['id' => 'd', 'text' => 'Ammeter'],
                        ],
                        'correct_option' => 'b',
                    ],
                    [
                        'id' => 19,
                        'question_text' => 'Which country gifted the Statue of Liberty to the USA?',
                        'options' => [
                            ['id' => 'a', 'text' => 'France'],
                            ['id' => 'b', 'text' => 'Germany'],
                            ['id' => 'c', 'text' => 'Italy'],
                            ['id' => 'd', 'text' => 'Canada'],
                        ],
                        'correct_option' => 'a',
                    ],
                    [
                        'id' => 20,
                        'question_text' => 'How many days are there in a leap year?',
                        'options' => [
                            ['id' => 'a', 'text' => '364'],
                            ['id' => 'b', 'text' => '365'],
                            ['id' => 'c', 'text' => '366'],
                            ['id' => 'd', 'text' => '367'],
                        ],
                        'correct_option' => 'c',
                    ],
                ],
            ],
        ];
    }
}
