<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{

    // ================= LIST USERS =================

    public function index(Request $request)
    {
        $query = User::query()
            ->select('id','name','email','role','status','created_at');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name','like',"%{$request->search}%")
                  ->orWhere('email','like',"%{$request->search}%");
            });
        }

        return response()->json(
            $query->latest()->paginate(20)
        );
    }

    // ================= CREATE USER =================

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,customer,restaurant,driver',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);

        return response()->json($user, 201);
    }

    // ================= UPDATE USER =================

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => "required|email|unique:users,email,$id",
            'role' => 'required|in:admin,customer,restaurant,driver',
        ]);

        $user->update($data);

        return response()->json($user);
    }

    // ================= DELETE USER =================

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot delete admin'
            ],403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    // ================= CHANGE STATUS =================

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,blocked'
        ]);

        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot change admin status'
            ],403);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Status updated',
            'user' => $user
        ]);
    }

    // ================= APPROVE DRIVER =================

    public function approveDriver($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'role' => 'driver',
            'status' => 'active'
        ]);

        return response()->json([
            'message' => 'Driver approved',
            'user' => $user
        ]);
    }
}
