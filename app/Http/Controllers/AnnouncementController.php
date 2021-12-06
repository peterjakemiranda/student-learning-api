<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $courseId = $request->input('course_id');
        $query = Announcement::query();
        if ($courseId) {
            $query->where('course_id', $courseId);
        }
        $announcements = $query->latest()->take(10)->get();

        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'nullable|string',
            'body' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $announcement = new Announcement();
        $announcement->title = $request->input('title');
        $announcement->body = $request->input('body');
        $announcement->course_id = $request->input('course_id');
        $announcement->save();

        return response()->json($announcement);
    }

    public function show($id)
    {
        $query = Announcement::query();
        $announcement = $query->findOrFail($id);

        return response()->json($announcement);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'body' => 'required|string',
            'course_id' => 'required|integer|exists:courses,id',
        ];
        $this->validate($request, $rules);

        $announcement = Announcement::findOrFail($id);
        $announcement->title = $request->title;
        $announcement->body = $request->body;
        $announcement->course_id = $request->input('course_id');
        $announcement->save();

        return response()->json($announcement);
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json('announcement removed successfully');
    }
}
