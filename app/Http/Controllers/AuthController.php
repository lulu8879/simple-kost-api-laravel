<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|numeric|max:2'
        ]);

        // Credit Value
        // 0 = Owner get 0 credit
        // 1 = Reguler get 20 credit
        // 2 = Premium get 40 credit
        $credit = null;
        if ($validatedData['role'] == 1) {
            $credit = 20;
        } elseif ($validatedData['role'] == 2) {
            $credit = 40;
        } else {
            $credit = 0;
        }
            
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'credit' => $credit
        ]);
            
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function login(Request $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login failed'],
                401
            );
        }
            
        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
        
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        
        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
