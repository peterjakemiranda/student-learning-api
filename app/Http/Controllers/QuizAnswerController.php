<?php

namespace App\Http\Controllers;

use App\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Quiz;
use App\QuizQuestion;
use App\QuizScore;

class QuizAnswerController extends Controller
{
    public function store(Request $request, $id)
    {
        $rules = [
            'answers' => 'required',
        ];
        $this->validate($request, $rules);
        foreach ($request->input('answers') as $question_id => $value) {
            $answer = QuizAnswer::updateOrCreate([
                'quiz_id' => $id,
                'question_id' => $question_id,
                'student_id' => auth()->id()],
            [
                'content' => $value
            ]);
        }

        $files = $request->file('answers');
        if ($files) {
            foreach($files as $question_id => $file) {
                $answer = QuizAnswer::firstOrNew([
                    'quiz_id' => $id,
                    'question_id' => $question_id,
                    'student_id' => auth()->id()]);

                $uploadDir = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'answers' . DIRECTORY_SEPARATOR;
                Storage::makeDirectory($uploadDir);
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs($uploadDir, $filename);
                $answer->file = 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'answers' . DIRECTORY_SEPARATOR . $filename;
                $answer->save();
            }
        }
        $quiz = Quiz::with(['answers' => function($query) {
                $query->where('student_id', auth()->id());
            }])->findOrFail($id);

        return response()->json($quiz);
    }

    public function score(Request $request, $id)
    {
        $rules = [
            'score' => 'required|integer',
            'student_id' => 'required|integer',
        ];
        $this->validate($request, $rules);

        $answer = QuizScore::updateOrCreate([
            'student_id' => $request->input('student_id'),
            'quiz_id' => $id],
            [
                'score' => $request->score,
                'scored_by' => auth()->id()
            ]
        );

        return response()->json($answer);


    }
}
