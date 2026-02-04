<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\User;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(
            User::where('role', 'driver')
                ->where('status', 'active')
                ->select('id', 'name', 'phone')
                ->orderBy('name')
                ->get()
        );
    }
}
