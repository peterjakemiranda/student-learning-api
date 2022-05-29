<?php

namespace App\Http\Controllers;

use App\Quiz;
use App\Traits\PushNotificaitonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class QuizController extends Controller
{
    use PushNotificaitonTrait;

    public function index(Request $request)
    {
        $courseId = $request->input('course_id');
        $query = Quiz::query();
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        if (auth()->user()->role === 'student') {
            $query->where(function($q) {
                $q->where('started', 1)
                    ->orWhereNotNull('started_date');
            })->with(['score' => function ($query) {
                $query->where('student_id', auth()->id());
            }])->with(['answers' => function ($query) {
                $query->where('student_id', auth()->id());
            }]);
        }
        $quizzes = $query->get();

        return response()->json($quizzes);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'points' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $quiz = new Quiz();
        $quiz->title = $request->input('title');
        $quiz->points = $request->input('points');
        $quiz->body = $request->input('body');
        $quiz->course_id = $request->input('course_id');
        $quiz->save();

        return response()->json($quiz);
    }

    public function toggle(Request $request, $id)
    {
        $rules = [
            'started' => 'required|integer',
        ];
        $this->validate($request, $rules);

        $quiz = Quiz::find($id);
        if ($request->input('started')) {
            $quiz->started_date = Carbon::now()->toDateTime();
        } else {
            $quiz->stopped_date = Carbon::now()->toDateTime();
        }
        $quiz->started = $request->input('started');
        if ($quiz->save()) {
            if($request->input('started')) {
                $this->sendQuizStartedNotification($quiz);
            }else{
                $this->sendQuizStoppedNotification($quiz);
            }
        }

        return response()->json($quiz->refresh());
    }

    public function archive(Request $request, $id)
    {
        $rules = [
            'archive' => 'required|integer',
        ];
        $this->validate($request, $rules);
        $quiz = Quiz::find($id);
        $quiz->archive = $request->input('archive') ? 1 : 0;
        $quiz->save();

        return response()->json($quiz->refresh());
    }

    public function show($id)
    {
        $query = Quiz::query();
        if ('student' === auth()->user()->role) {
            $query->with(['answers' => function ($query) {
                $query->where('student_id', auth()->id());
            }]);
        }
        $quiz = $query->findOrFail($id);

        return response()->json($quiz);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'points' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $quiz = Quiz::findOrFail($id);

        $quiz->title = $request->title;
        $quiz->points = $request->points;
        $quiz->body = $request->body;
        $quiz->course_id = $request->input('course_id');
        $quiz->save();

        return response()->json($quiz);
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        return response()->json('quiz removed successfully');
    }
}
