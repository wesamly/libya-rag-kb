<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentGapController extends Controller
{
    public function index(Request $request)
    {
        // This query finds all questions where the AI said it didn't know the answer,
        // groups them, and counts how many times they were asked.
        $query = ChatHistory::query()
            ->select('user_question', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_asked'))
            ->where('ai_response', 'like', "%I'm sorry, I don't have information%")
            // ->whereDate('created_at', '>=', now()->subDays($request->input('days', 30))) // Example filter
            ->groupBy('user_question')
            ->orderBy('count', 'desc');

        $gaps = $query->paginate(20);

        return $gaps;
    }
}