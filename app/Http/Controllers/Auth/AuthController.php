<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string',
            'password' => 'required|min:6',
            'role'     => 'required|in:customer,restaurant,delivery'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        $token = substr(bin2hex(random_bytes(2)), 0, 4);
        return response()->json([
            'message' => 'User registered successfully',
            'token'   => $token,
            'user'    => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }
        // $token = substr(bin2hex(random_bytes(2)), 0, 4);
        return response()->json([
            'token' => $token,
            'user'  => auth('api')->user()
        ]);
    }

    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
