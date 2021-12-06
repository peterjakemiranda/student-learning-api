<?php

namespace App\Http\Controllers;

use App\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $query = Course::latest()->with(['announcements', 'activities' => function($query) {
                $query->where('due_date', '>=', Carbon::now())
                    ->where('due_date', '<=', Carbon::now()->addDay());
            }, 'quizzes' => function($query) {
                $query->where('started', 1);
            }]);
        if (auth()->user()->role === 'teacher') {
            $query->where('user_id', auth()->id())
                ->withCount(['students']);
        }
        if (auth()->user()->role === 'student') {
            $query->whereExists(function($query)
                {
                    $query->select(DB::raw(1))
                          ->from('course_user')
                          ->whereRaw('course_user.course_id = courses.id')
                          ->where('course_user.user_id', auth()->id());
                });
        }

        $courses = $query->get();

        $courses->map(function($course) {
            $course->setRelation('announcements', $course->announcements->take(3));
            return $course;
        });

        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'course_code' => 'nullable|string',
            'period' => 'nullable|string',
            'virtual_class_link' => 'nullable|string',
            'description' => 'nullable|string',
            'start' => 'nullable|string',
            'end' => 'nullable|string',
        ];
        $this->validate($request, $rules);

        $course = new Course();
        $course->user_id = auth()->id();
        $course->title = $request->input('title');
        $course->course_code = $request->input('course_code');
        $course->period = $request->input('period');
        $course->virtual_class_link = $request->input('virtual_class_link');
        $course->description = $request->input('description');
        $course->start = $request->input('start');
        $course->end = $request->input('end');
        $course->save();

        return response()->json($course);
    }

    public function show($id)
    {
        $course = Course::withCount('students')
            ->with(['announcements', 'activities' => function($query) {
                $query->where('due_date', '>=', Carbon::now())
                    ->where('due_date', '<=', Carbon::now()->addDay());
            }, 'quizzes' => function($query) {
                $query->where('started', 1);
            }])
            ->findOrFail($id);

        $course->setRelation('announcements', $course->announcements->take(3));
        return response()->json($course);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required|string',
            'course_code' => 'nullable|string',
            'period' => 'nullable|string',
            'virtual_class_link' => 'nullable|string',
            'description' => 'nullable|string',
            'start' => 'nullable|string',
            'end' => 'nullable|string',
        ];
        $this->validate($request, $rules);

        $course = Course::findOrFail($id);

        $course->title = $request->input('title');
        $course->course_code = $request->input('course_code');
        $course->period = $request->input('period');
        $course->virtual_class_link = $request->input('virtual_class_link');
        $course->description = $request->input('description');
        $course->start = $request->input('start');
        $course->end = $request->input('end');
        $course->save();

        return response()->json($course);
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json('course removed successfully');
    }
}
