<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatHistory::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('user_question', 'like', "%{$search}%")
                  ->orWhere('ai_response', 'like', "%{$search}%");
            });
        }
        
        // Filter
        if ($request->filled('feedback') && $request->feedback !== 'all') {
            $query->where('user_feedback', $request->feedback);
        }

        $logs = $query->latest()->paginate(20);
        return $logs;
    }

    public function show($id)
    {
        $log = ChatHistory::findOrFail($id);

        $retrieved_articles = [];
        
        // If we have IDs, fetch the actual Article objects from MySQL
        if (!empty($log->retrieved_article_ids)) {
             $retrieved_articles = Article::whereIn('id', $log->retrieved_article_ids)
                            ->get(['id', 'title', 'slug', 'content']); // Fetch content for preview
        }
        
        return response()->json([
            'log' => $log,
            'retrieved_articles' => $retrieved_articles,
        ]);
    }
}