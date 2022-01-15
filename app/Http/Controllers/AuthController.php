<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ];

        $this->validate($request, $validationRules);

        $remember_token = app('hash')->make($request->email.$request->password);
        $remember_token = preg_replace('/[^A-Za-z0-9\-]/', '', $remember_token);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = app('hash')->make($request->password);
        $user->remember_token = $remember_token;
        $user->save();

        // TODO: Send email verification

        return response()->json([
            'message' => 'User has been created',
            'data' => ['user' => $user]
        ], 200);
    }

    public function login(Request $request)
    {
        $validationRules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $this->validate($request, $validationRules);

        $user = User::where('email', $request->email)->first();

        if(!$user->verified_at) {
            throw new AuthorizationException('User not activated');
        }


        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            throw new AuthorizationException('Invalid credentials');
        }

        return response()->json([
            'message' => 'User logged in',
            'data' => [
                'token' => $token,
                'expired_in' => Auth::factory()->getTTL() * 60,
                'token_type' => 'bearer'
            ]
        ], 200);
    }

    function confirmation(Request $request, $token)
    {
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $user->verified_at = date('Y-m-d H:i:s');
        $user->save();

        return response()->json([
            'message' => 'User has been activated',
            'data' => ['user' => $user]
        ], 200);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json([
            'message' => 'User logged out'
        ], 200);
    }

    function forgotPassword(Request $request)
    {
        $validationRules = [
            'email' => 'required|email'
        ];

        $this->validate($request, $validationRules);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $remember_token = app('hash')->make($request->email.$user->password);
        $remember_token = preg_replace('/[^A-Za-z0-9\-]/', '', $remember_token);
        $user->remember_token = $remember_token;
        $user->save();

        // TO DO: Send email with reset password token

        return response()->json([
            'message' => 'Password reset link has been sent',
            'data' => ['user' => $user]
        ], 200);
    }

    function resetPassword(Request $request, $forgotToken)
    {
        $validationRules = [
            'password' => 'required|confirmed'
        ];

        $this->validate($request, $validationRules);

        $user = User::where('remember_token', $forgotToken)->first();

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $user->password = app('hash')->make($request->password);
        $user->remember_token = null;
        $user->save();

        return response()->json([
            'message' => 'Password has been reset',
            'data' => ['user' => $user]
        ], 200);
    }
}
