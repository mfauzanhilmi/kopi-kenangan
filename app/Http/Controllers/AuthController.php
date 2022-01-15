<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validationRules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ];
        // throw new \Exception('Registration is not yet implemented');

        $this->validate($request, $validationRules);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = app('hash')->make($request->password);
        $user->save();

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

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
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
}
