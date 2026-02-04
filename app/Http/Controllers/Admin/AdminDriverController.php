<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverRequest;

class AdminDriverController extends Controller
{

    public function index()
    {
        return DriverRequest::with('user')
            ->where('status', 'pending')
            ->get();
    }

    public function approve($id)
    {
        $request = DriverRequest::findOrFail($id);

        $request->update([
            'status' => 'approved'
        ]);

        $request->user->update([
            'role' => 'driver'
        ]);

        return response()->json([
            'message' => 'Driver approved'
        ]);
    }
}

