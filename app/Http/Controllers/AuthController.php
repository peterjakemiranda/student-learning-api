<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    /**
     * Attempt to register a new user to the API.
     *
     * @return Response
     */
    public function register(Request $request)
    {
        // Are the proper fields present?
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|confirmed|string|min:4',
        ]);
        try {
            $user = new User();
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->save();

            $credentials = $request->only(['email', 'password']);
            if (!$token = Auth::attempt($credentials, true)) {
                // Login has failed
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            return $this->respondWithToken($token);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Attempt to authenticate the user and retrieve a JWT.
     * Note: The API is stateless. This method _only_ returns a JWT. There is not an
     * indicator that a user is logged in otherwise (no sessions).
     *
     * @return Response
     */
    public function login(Request $request)
    {
        // Are the proper fields present?
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::attempt($credentials, true)) {
            // Login has failed
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token). Requires a login to use as the
     * JWT in the Authorization header is what is invalidated.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh the current token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $token = auth()->refresh();
        } catch (TokenInvalidException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Invalid token ' . $e->getMessage(),
                    'code' => 40103,
                    'status_code' => Response::HTTP_UNAUTHORIZED,
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Helper function to format the response with the token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'role' => auth()->user()->role ?? 'student',
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            ], 200);
    }
}
