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
        $query = ChatHistory::query()
            ->select('user_question', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_asked'))
            ->where('ai_response', 'like', "%I'm sorry, I don't have information%");

        $this->applyFilters($query, $request);

        $query->groupBy('user_question')
              ->orderBy('count', 'desc');

        return $query->paginate(20);
    }

    public function export(Request $request)
    {
        $query = ChatHistory::query()
            ->select('user_question', DB::raw('COUNT(*) as count'), DB::raw('MAX(created_at) as last_asked'))
            ->where('ai_response', 'like', "%I'm sorry, I don't have information%");

        $this->applyFilters($query, $request);

        $query->groupBy('user_question')
              ->orderBy('count', 'desc');

        $gaps = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=content_gap_export.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($gaps) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User Question', 'Count', 'Last Asked']);

            foreach ($gaps as $gap) {
                fputcsv($file, [$gap->user_question, $gap->count, $gap->last_asked]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyFilters($query, Request $request)
    {
        $range = $request->input('date_range', '30d');

        if ($range === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        } elseif ($range === '7d') {
            $query->whereDate('created_at', '>=', now()->subDays(7));
        } elseif ($range === '30d') {
            $query->whereDate('created_at', '>=', now()->subDays(30));
        } elseif ($range === 'quarter') {
             $query->whereDate('created_at', '>=', now()->firstOfQuarter());
        }
    }
}