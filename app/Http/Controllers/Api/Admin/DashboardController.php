<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http; // Use HTTP facade to call Python API
use Symfony\Component\Process\Process;

class DashboardController extends Controller
{
    public function getStats()
    {
        try {
            // --- 1. MySQL Stats (Local) ---
            $articlesToSync = Article::where('needs_sync', true)->where('status', 'published')->count();
            $totalPublished = Article::where('status', 'published')->count();
            
            $chatQueries24h = ChatHistory::where('created_at', '>=', now()->subDay())->count();
            
            $negativeFeedback24h = ChatHistory::where('user_feedback', 'bad')
                                        ->where('created_at', '>=', now()->subDay())
                                        ->count();

            $lastSync = Article::where('status', 'published')
                            ->where('needs_sync', false)
                            ->max('last_synced_at');

            // --- 2. Vector DB Stats (From Python API) ---
            // We call the Python service to get the ChromaDB count
            $totalChunks = 0;
            $vectorDbStatus = 'Unknown';
            
            try {
                // This assumes you added the GET /stats endpoint to your main.py
                $pythonApiUrl = env('PYTHON_RAG_API_URL', 'http://127.0.0.1:8000');
                // Remove '/ask' if it's in the base URL, we want the root or /stats
                $baseUrl = str_replace('/ask', '', $pythonApiUrl);
                
                $response = Http::timeout(2)->get($baseUrl . '/stats');
                
                if ($response->successful()) {
                    $data = $response->json();
                    $totalChunks = $data['total_chunks'] ?? 0;
                    $vectorDbStatus = 'Operational';
                } else {
                    $vectorDbStatus = 'Error (API)';
                }
            } catch (\Exception $e) {
                // If Python API is down, don't crash the dashboard
                $vectorDbStatus = 'Offline';
                Log::warning('Dashboard could not connect to Python API: ' . $e->getMessage());
            }

            $pipelineStatus = [
                'vector_db_status' => $vectorDbStatus,
                'total_chunks' => $totalChunks,
                'last_sync' => $lastSync ? $lastSync : 'Never',
            ];

            return response()->json([
                'articles_to_sync' => $articlesToSync,
                'total_published' => $totalPublished,
                'chat_queries_24h' => $chatQueries24h,
                'negative_feedback_24h' => $negativeFeedback24h,
                'pipeline_status' => $pipelineStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard Stats Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch dashboard stats.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecentActivity()
    {
        try {
            $activity = ChatHistory::latest()
                ->take(5)
                ->get(['id', 'session_id', 'user_question', 'ai_response', 'user_feedback', 'created_at']);
            
            // Format for the UI
            $activity->transform(function ($item) {
                // Create a fake user ID from the session ID for display
                $item->user = 'user_' . substr(md5($item->session_id), 0, 6);
                return $item;
            });

            return response()->json($activity);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch recent activity.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function runSync()
    {
        $pythonPath = env('PYTHON_PATH');
        $scriptPath = env('INGEST_SCRIPT_PATH');
        
        if (!$pythonPath || !$scriptPath) {
            return response()->json(['error' => 'Python script paths not configured in .env file.'], 500);
        }

        try {
            // Run the ingestion script in the background
            $process = new Process([$pythonPath, $scriptPath]);
            $process->start(); // start() runs asynchronously

            return response()->json(['message' => 'Sync process started successfully.']);

        } catch (\Exception $e) {
            Log::error('Sync Script Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to start sync process.', 'message' => $e->getMessage()], 500);
        }
    }
}