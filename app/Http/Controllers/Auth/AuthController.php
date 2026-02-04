<?php

namespace App\Http\Controllers\Auth;
use App\Models\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
{
    $validator = Validator::make(
        $request->all(),
        [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string',
            'password' => 'required|min:6',
            'role' => 'required|in:customer,restaurant,driver,admin'
        ]
    );

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'role' => $request->role,
        'password' => Hash::make($request->password),
    ]);

    // ⭐ إنشاء مطعم تلقائياً
    if ($user->role === 'restaurant') {

        Restaurant::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'address' => 'Not set yet',
        ]);
    }

    $token = JWTAuth::fromUser($user);

    return response()->json([
        'message' => 'User registered successfully',
        'token' => $token,
        'user' => $user
    ], 201);
}

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $user = JWTAuth::setToken($token)->toUser();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        if ($user->status !== 'active') {
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Account is blocked'], 403);
        }
        return response()->json(['token' => $token, 'user' => $user], 200);
    }
    public function profile()
    {
        return response()->json(auth('api')->user());
    }
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Logged out successfully']);
    }
}
