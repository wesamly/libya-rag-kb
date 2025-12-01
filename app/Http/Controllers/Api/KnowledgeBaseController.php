<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use App\Models\ChatHistory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\TocGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http; // For proxying to Python

class KnowledgeBaseController extends Controller
{
    // Fetches categories and popular articles for the home page.
    public function getHomeData()
    {
        $categories = Category::take(4)->get(['name', 'slug', 'description', 'icon']);
        
        // "Popular" is just "latest" for now.
        $popularArticles = Article::published()
                            ->latest()
                            ->take(4)
                            ->get(['title', 'slug']);
        
        // We'll add the icon manually for the front-end
        $popularArticles->transform(function ($article) {
            $article->icon = 'bi-file-text';
            return $article;
        });

        return response()->json([
            'categories' => $categories,
            'popularArticles' => $popularArticles
        ]);
    }

    // Fetches all categories for the sidebar.
    public function getCategories()
    {
        $categories = Category::orderBy('name')->get(['name', 'slug', 'icon']);
        return response()->json($categories);
    }

    // Fetches a single category and its paginated articles.
    public function getCategoryDetails(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $articles = $category->articles()
                            ->published()
                            ->latest()
                            ->paginate(10, ['title', 'slug', 'content']); // We'll paginate

        // Create a simple description
        $articles->getCollection()->transform(function ($article) {
            $article->description = Str::limit(strip_tags($article->content), 150);
            unset($article->content); // Don't send full content
            return $article;
        });

        return response()->json([
            'category' => [
                'name' => $category->name,
                'description' => $category->description
            ],
            'articles' => $articles // This is now a paginated object
        ]);
    }

    // Fetches a single article and related articles.
    public function getArticleDetails(Request $request, TocGenerator $tocGen, $slug)
    {
        $article = Article::published()->where('slug', $slug)->firstOrFail();
        
        // Get 3 related articles from the same category
        $related_articles = Article::published()
                            ->where('category_id', $article->category_id)
                            ->where('id', '!=', $article->id) // Exclude self
                            ->take(3)
                            ->get(['title', 'slug', 'content']);
        
        $related_articles->transform(function ($item) {
            $item->description = Str::limit(strip_tags($item->content), 80);
            unset($item->content);
            return $item;
        });

        $result = $tocGen->generate($article->content);

        $toc = $result['toc'];

        $breadcrumb = [
            ['name' => 'Home', 'slug' => '/'],
            ['name' => $article->category->name, 'slug' => '/categories/' . $article->category->slug],
            ['name' => $article->title, 'slug' => null],
        ];

        return response()->json([
            'article' => [
                'title' => $article->title,
                'category' => ['name' => $article->category->name, 'slug' => $article->category->slug],
                'breadcrumb' => $breadcrumb,
                'last_updated' => $article->updated_at->format('F d, Y'),
                'read_time' => ceil(str_word_count(strip_tags($article->content)) / 200) . ' min read',
                'toc' => $toc,
                'content' => $result['content'], // Send full HTML content
            ],
            'related_articles' => $related_articles
        ]);
    }

    // Performs a full-text search.
    public function getSearchResults(Request $request)
    {
        $query = $request->input('q', '');
        $categorySlug = $request->input('category', 'all');

        $articleQuery = Article::query()->published();

        // 1. Add Full-Text Search
        if ($query) {
            $articleQuery->whereRaw(
                "MATCH(title, content) AGAINST(? IN BOOLEAN MODE)", 
                [$query . '*'] // Use boolean mode for partial matching
            );
        }

        // 2. Add Category Filter
        if ($categorySlug !== 'all') {
            $articleQuery->whereHas('category', function (Builder $q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // 3. Paginate results
        $results = $articleQuery->paginate(10, ['id', 'title', 'slug', 'content', 'category_id']);

        // 4. Format results
        $results->getCollection()->transform(function ($article) use ($query) {
            $article->breadcrumb = 'in ' . $article->category->name;
            
            // Create a snippet. Note: This is a simple version.
            $snippet = Str::limit(strip_tags($article->content), 200);
            
            // Highlight the query
            if ($query) {
                $snippet = preg_replace('/('.preg_quote($query, '/').')/i', '<strong>$1</strong>', $snippet);
            }

            $article->snippet = '...' . $snippet . '...';
            $article->image = 'https://placehold.co/600x400/1D2939/FFFFFF?text=' . Str::slug($article->title);
            unset($article->content);
            unset($article->category);
            return $article;
        });

        // 5. Get category filter data (this is a bit complex, but good for UX)
        $categories = Category::withCount(['articles' => fn($q) => $q->published()])
                        ->get(['name', 'slug', 'articles_count'])
                        ->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name, 'count' => $c->articles_count]);

        return response()->json([
            'query' => $query,
            'count' => $results->total(),
            'results' => $results,
            'categories' => $categories
        ]);
    }

    // This proxies the chat request to your Python API
    public function handleChat(Request $request)
    {
        // Get the Python API URL from your .env
        $pythonApiUrl = env('PYTHON_RAG_API_URL', 'http://127.0.0.1:8000/ask');

        try {
            $response = Http::timeout(60)->post($pythonApiUrl, [
                'question' => $request->input('question'),
                'session_id' => $request->session()->getId(), // Pass session ID
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'AI service failed'], 500);
            }
            
            $aiData = $response->json();
            $answer = $aiData['answer'];
            $retrieved_ids = $aiData['retrieved_article_ids'] ?? [];

            // 1. Log the conversation to MySQL
            ChatHistory::create([
                'session_id' => $request->session()->getId(),
                'user_question' => $request->input('question'),
                'ai_response' => $answer,
                'retrieved_article_ids' => $retrieved_ids,
            ]);

            // 2. Fetch slugs for the source links
            $sources = Article::whereIn('id', $retrieved_ids)
                            ->get(['id', 'title', 'slug']);

            return response()->json([
                'answer' => $answer,
                'sources' => $sources
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
