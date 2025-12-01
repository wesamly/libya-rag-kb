<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatHistory;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Get the full context from Postgres
        $context = [];
        if (!empty($log->retrieved_article_ids)) {
            try {
                $chunks = DB::connection('pgsql')->table('article_vectors')
                    ->whereIn('mysql_id', $log->retrieved_article_ids)
                    ->get(['mysql_id', 'chunk_text']);
                
                // Group chunks by article ID
                foreach ($chunks as $chunk) {
                    $context[] = [
                        'article_id' => $chunk->mysql_id,
                        'text' => Str::limit($chunk->chunk_text, 300)
                    ];
                }
            } catch (\Exception $e) {
                report($e);
                $context[] = ['article_id' => 0, 'text' => 'Error: Could not connect to vector DB.'];
            }
        }
        
        // Get Article titles
        $articles = Article::whereIn('id', $log->retrieved_article_ids ?? [])
                        ->get(['id', 'title', 'slug']);

        return response()->json([
            'log' => $log,
            'retrieved_articles' => $articles,
            'retrieved_context'G' => $context,
        ]);
    }
}