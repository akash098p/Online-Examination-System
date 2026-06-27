<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Exam;
use App\Models\Option;

class QuestionController extends Controller
{
    /**
     * List questions of an exam
     */
    public function index(Exam $exam)
    {
        $questions = $exam->questions()
            ->with('options')
            ->withCount('options')
            ->orderBy('id')
            ->get();

        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show add question page
     */
    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    /**
     * Store a new question with options
     */
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text'   => 'required|string',
            'topic'           => 'nullable|string|max:255',
            'explanation'     => 'nullable|string',
            'marks'           => 'nullable|integer|min:1',
            'options'         => 'required|array|min:2',
            'options.*'       => 'required|string',
            'correct_option'  => 'required|integer',
        ]);

        // Create question
        $question = $exam->questions()->create([
            'question_text' => $request->question_text,
            'topic' => $request->topic,
            'marks' => $request->marks,
            'explanation' => $request->explanation ?? null,
        ]);

        // Save options
        foreach ($request->options as $index => $text) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct'  => ((int)$index === (int)$request->correct_option),
            ]);
        }

        return redirect()
            ->route('admin.questions.edit', $question)
            ->with('success', 'Question saved. Review or add next.');
    }

    /**
     * Edit question
     */
    public function edit(Question $question)
    {
        $question->load('options');

        // Previous question
        $prev = Question::where('exam_id', $question->exam_id)
            ->where('id', '<', $question->id)
            ->orderBy('id', 'desc')
            ->first();

        // Next question
        $next = Question::where('exam_id', $question->exam_id)
            ->where('id', '>', $question->id)
            ->orderBy('id')
            ->first();

        return view('admin.questions.edit', compact('question', 'prev', 'next'));
    }

    /**
     * Update question and options
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text'   => 'required|string',
            'topic'           => 'nullable|string|max:255',
            'explanation'     => 'nullable|string',
            'marks'           => 'nullable|integer|min:1',
            'options'         => 'required|array|min:2',
            'options.*'       => 'required|string',
            'correct_option'  => 'required|integer',
        ]);

        // Update question
        $question->update([
            'question_text' => $request->question_text,
            'topic' => $request->topic,
            'marks' => $request->marks ?? $question->marks,
            'explanation' => $request->explanation,
        ]);

        // Remove old options
        $question->options()->delete();

        // Save new options
        foreach ($request->options as $index => $text) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct'  => ((int)$index === (int)$request->correct_option),
            ]);
        }

        return back()->with('success', 'Question updated.');
    }

    /**
     * Delete question
     */
    public function destroy(Question $question)
    {
        $exam = $question->exam;
        $question->delete();

        return redirect()
            ->route('admin.questions.index', $exam)
            ->with('success', 'Question deleted.');
    }
}
