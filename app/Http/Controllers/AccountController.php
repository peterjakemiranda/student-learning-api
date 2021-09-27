<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display the current user account.
     *
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Update account details
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:100',
            'password' => 'nullable|confirmed|string|min:6',
        ]);
        try {
            $user = auth()->user();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            if($plainPassword = $request->input('password')){
                $user->password = app('hash')->make($plainPassword);
            }
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Account update Failed!'], 400);
        }
        return response()->json(['message' => 'Account updated'], 200);
    }
}
