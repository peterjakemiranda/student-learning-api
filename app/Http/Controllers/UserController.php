<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\UserCreated;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->input('search')) {
            $query->where(function($q) use($request) {
                $q->where('first_name', 'LIKE', '%' . $request->get('search') . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request->get('search') . '%')
                ->orWhere('email', 'LIKE', '%' . $request->get('search') . '%');
            });
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

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'role' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'nullable|confirmed|min:6',
        ];
        $this->validate($request, $rules);

        $user = new User();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $plainPassword = $request->input('password') ?: Str::random(6);
        $user->password = app('hash')->make($plainPassword);
        $user->save();

        Mail::send(new UserCreated($user, $plainPassword));

        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'role' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users,email,'.$id,
            'password' => 'nullable|confirmed|min:6',
        ];
        $this->validate($request, $rules);

        $user = User::findOrFail($id);
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->role = $request->input('role');
        $plainPassword = $request->input('password') ?: Str::random(6);
        $user->password = app('hash')->make($plainPassword);
        $user->save();

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json('user removed successfully');
    }

}
