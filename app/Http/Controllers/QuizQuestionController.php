<?php

namespace App\Http\Controllers;

use App\Quiz;
use App\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuizQuestionController extends Controller
{
    public function index(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $query = QuizQuestion::query();
        if ($quizId) {
            $query->where('quiz_id', $quizId);
        }
        if (auth()->user()->role == 'student' || $request->input('student_id')) {
            $studentId = $request->input('student_id') ?: auth()->id();
            $query->with(['answer' => function($q) use($studentId) {
                        $q->where('student_id', $studentId);
                    }]);
        }
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|string',
            'question' => 'required|string',
            'options' => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'quiz_id' => 'required|integer|exists:quizzes,id',
        ];
        $this->validate($request, $rules);

        $quiz = new QuizQuestion();
        $quiz->type = $request->input('type');
        $quiz->question = $request->input('question');
        $quiz->options = $request->input('options', []);
        $quiz->correct_answer = $request->input('correct_answer');
        $quiz->quiz_id = $request->input('quiz_id');
        $quiz->save();

        return response()->json($quiz);
    }

    public function show($id)
    {
        $quiz = QuizQuestion::findOrFail($id);

        return response()->json($quiz);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'type' => 'required|string',
            'question' => 'required|string',
            'options' => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'quiz_id' => 'required|integer|exists:quizzes,id',
        ];
        $this->validate($request, $rules);

        $quiz = QuizQuestion::findOrFail($id);

        $quiz->type = $request->input('type');
        $quiz->question = $request->input('question');
        $quiz->options = $request->input('options');
        $quiz->correct_answer = $request->input('correct_answer');
        $quiz->quiz_id = $request->input('quiz_id');
        $quiz->save();

        return response()->json($quiz);
    }

    public function destroy($id)
    {
        $quiz = QuizQuestion::findOrFail($id);
        $quiz->delete();

        return response()->json('quiz removed successfully');
    }
}
