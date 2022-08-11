<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed']
        ]);
        
        $user = User::create([
            'name' => $request['email'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('usertoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $user = User::where('email', $fields['email'])->first();
        
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken('usertoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        
        return response([
            'message' => 'Logged Out'
        ], 200);
    }
}
