<?php

namespace App\Http\Controllers;

use App\Mail\StudentInvited;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Course;
use App\Mail\StudentAddedToCourse;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student');

        if ($request->input('search')) {
            $query->where(function($q) use($request) {
                $q->where('first_name', 'LIKE', '%' . $request->get('search') . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request->get('search') . '%')
                ->orWhere('email', 'LIKE', '%' . $request->get('search') . '%');
            });
        }

        if ($courseId = $request->input('course_id')) {
            $qfunction = $request->input('by_course') ? 'whereExists' : 'whereNotExists';
            $query->{$qfunction}(function($query) use ($courseId)
                {
                    $query->select(DB::raw(1))
                          ->from('course_user')
                          ->whereRaw('course_user.user_id = users.id')
                          ->where('course_user.course_id', $courseId);
                });
        }
        if ($activityId = $request->input('activity_id')) {
            $query->with(['answers' => function($q) use($activityId) {
                        $q->where('activity_id', $activityId);
                    }]);
        }

        if ($quizId = $request->input('quiz_id')) {
            $query->with([
                'quiz_answers' => function($q) use($quizId) {
                        $q->where('quiz_id', $quizId);
                    },
                'quiz_score' => function($q) use($quizId) {
                        $q->where('quiz_id', $quizId);
                    }
                ]);
        }

        if (!$request->input('limit')) {
            return response()->json($query->get());
        }

        $this->setPagination($request->input('limit'));
        $pagination = $query->paginate($this->getPagination());
        $data = [
            'data' => $pagination->items(),
            'pagination' => [
                'last_page' => $pagination->lastPage(),
                'current_page' => $pagination->currentPage(),
                'limit' => $pagination->perPage(),
                'total_count' => $pagination->total(),
            ],
        ];

        return response()->json($data);
    }
    
    public function add(Request $request)
    {
        $this->validate($request, [
            'student_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        try {
            $course = Course::find($request->input('course_id'));
            $user = User::find($request->input('student_id'));
            $user->courses()->syncWithoutDetaching($request->input('course_id'));
            
            Mail::send(new StudentAddedToCourse($user, auth()->user(), $course));

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
        return response()->json(['message' => 'Student successfully added']);
    }

    /**
     * Invite student
     *
     * @return Response
     */
    public function invite(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'course_id' => 'required|integer',
            'email' => 'required|string|email|max:100|unique:users',
        ]);
        try {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $user->role = User::ROLE_STUDENT;
            $plainPassword = Str::random(6);
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            $course = Course::find($request->input('course_id'));
            Mail::send(new StudentInvited($user, auth()->user(), $course, $plainPassword));

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        return response()->json(['message' => 'Student successfully added']);

    }

}
