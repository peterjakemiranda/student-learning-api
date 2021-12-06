<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Traits\PushNotificaitonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\User;

class ActivityController extends Controller
{
    use PushNotificaitonTrait;

    public function index(Request $request)
    {
        $chapterId = $request->input('course_id');
        $query = Activity::query();
        if ($chapterId) {
            $query->where('course_id', $chapterId);
        }
        if (auth()->user()->role === 'student') {
            $query->with(['answers' => function($query) {
                $query->where('student_id', auth()->id());
            }])
            ->where('draft', 0);
        }
        if(auth()->user()->role === 'teacher') {
            $query->withCount('answers');
        }
        $activities = $query->get();

        return response()->json($activities);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'points' => 'required|string',
            'submission_type' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $activity = new Activity();
        $activity->title = $request->input('title');
        $activity->points = $request->input('points');
        // $activity->display_date = $request->input('display_date');
        $activity->due_date = $request->input('due_date');
        $activity->submission_type = $request->input('submission_type');
        $activity->body = $request->input('body');
        $activity->course_id = $request->input('course_id');
        $activity->draft = $request->input('draft', 0);

        if ($request->hasFile('file')) {
            $uploadDir = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
            Storage::makeDirectory($uploadDir);
            $file = $request->file('file');
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->storeAs($uploadDir, $filename);
            $activity->file = 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $filename;
        }
        if ($activity->save() && !$request->input('draft')) {
            $this->sendActivityAddedNotification($activity);
        }

        return response()->json($activity);
    }

    public function show($id)
    {
        $query = Activity::query();
        if (auth()->user()->role === 'student'){
            $query->with(['answers' => function($query) {
                $query->where('student_id', auth()->id());
            }]);
        }
        $activity = $query->findOrFail($id);

        return response()->json($activity);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'points' => 'required|string',
            'submission_type' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $activity = Activity::findOrFail($id);
        $isDraftOriginally = $activity->draft;
        $activity->title = $request->input('title');
        $activity->points = $request->input('points');
        $activity->due_date = $request->input('due_date');
        $activity->submission_type = $request->input('submission_type');
        $activity->body = $request->input('body');
        $activity->course_id = $request->input('course_id');
        $activity->draft = $request->input('draft', 0);
        if ($request->hasFile('file')) {
            $uploadDir = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
            Storage::makeDirectory($uploadDir);
            $file = $request->file('file');
            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->storeAs($uploadDir, $filename);
            $activity->file = 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $filename;
        }
        if ($activity->save()) {
           if( $request->input('draft') == 0 && $request->input('draft') != $isDraftOriginally) {
               $this->sendActivityAddedNotification($activity);
           }
        }

        return response()->json($activity);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        $activity->delete();

        return response()->json('activity removed successfully');
    }
}
