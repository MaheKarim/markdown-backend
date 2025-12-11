<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Document;

class AdminController extends Controller
{
    /**
     * Get dashboard metrics
     */
    public function dashboard(): JsonResponse
    {
        $totalUsers = User::count();
        $totalDocuments = Document::count();
        
        return response()->json([
            'metrics' => [
                'total_users' => $totalUsers,
                'total_documents' => $totalDocuments,
            ],
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            'recent_documents' => Document::with('user:id,name')->latest()->take(5)->get(['id', 'title', 'user_id', 'created_at']),
        ]);
    }
}
