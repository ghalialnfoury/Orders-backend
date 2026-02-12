<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverRequest;
use Illuminate\Http\JsonResponse;

class AdminDriverRequestController extends Controller
{

    // عرض كل الطلبات المعلقة
    public function index(): JsonResponse
    {
        $requests = DriverRequest::with('user')
            ->where('status', 'pending')
            ->get();

        return response()->json($requests);
    }

    // موافقة الأدمن
    public function approve($id): JsonResponse
    {
        $driverRequest = DriverRequest::findOrFail($id);

        if ($driverRequest->status !== 'pending') {
            return response()->json([
                'message' => 'Request already processed'
            ], 400);
        }

        // تحديث حالة الطلب
        $driverRequest->update([
            'status' => 'approved'
        ]);

        // تحويل المستخدم لسائق
        $driverRequest->user->update([
            'role' => 'driver'
        ]);

        return response()->json([
            'message' => 'Driver approved successfully'
        ]);
    }
}
