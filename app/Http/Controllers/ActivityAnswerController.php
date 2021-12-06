<?php

namespace App\Http\Controllers;

use App\ActivityAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Activity;

class ActivityAnswerController extends Controller
{
    public function store(Request $request, $id)
    {
        $rules = [
            'submission_type' => 'required|string',
            'content' => 'required_if:submission_type,==,file_upload|string',
            'file' => 'required_if:submission_type,==,text_entry',
        ];
        $this->validate($request, $rules);

        $answer = ActivityAnswer::firstOrNew([
            'activity_id' => $id,
            'student_id' => auth()->id(), ]);

        if ($request->input('content')){
            $answer->content = $request->input('content');
        }

        if ($request->hasFile('file')) {
            $uploadDir = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'answers' . DIRECTORY_SEPARATOR;
            Storage::makeDirectory($uploadDir);
            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($uploadDir, $filename);
            $answer->file = 'storage' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'answers' . DIRECTORY_SEPARATOR . $filename;
        }
        $answer->save();

        $activity = Activity::with(['answers' => function($query) {
                $query->where('student_id', auth()->id());
            }])->findOrFail($id);

        return response()->json($activity);
    }

    public function score(Request $request, $id, $answerId)
    {
        $rules = [
            'score' => 'required|integer',
        ];
        $this->validate($request, $rules);

        $answer = ActivityAnswer::findOrFail($answerId);
        $answer->score = $request->input('score');
        $answer->scored_by = auth()->id();
        $answer->save();

        return response()->json($answer);
    }
}
