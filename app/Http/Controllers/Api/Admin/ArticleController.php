<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::query()->with('category');

        // Filter by Category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Sync Status
        if ($request->filled('sync')) {
            $query->where('needs_sync', $request->sync);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $articles = $query->latest('updated_at')->paginate(10);
        return $articles;
    }

    public function show($id)
    {
        $article = Article::with('category', 'tags')->findOrFail($id);
        
        // Format for the tag input
        $article->tags = $article->tags->map(fn($tag) => [
            'value' => $tag->id,
            'label' => $tag->name,
        ]);
        
        return $article;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array'
        ]);

        $article = new Article([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'author_id' => auth()->id(),
            'slug' => Str::slug($validated['title']),
            'needs_sync' => true, // Mark for sync
        ]);
        $article->save();

        $this->syncTags($article, $validated['tags'] ?? []);

        return response()->json($article, 201);
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
            'tags' => 'nullable|array'
        ]);
        
        $article->fill([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'slug' => Str::slug($validated['title']),
            'needs_sync' => true, // Mark for sync on every update
        ]);
        $article->save();

        $this->syncTags($article, $validated['tags'] ?? []);

        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        // We also need to delete the vectors for this article.
        // This should be handled by a queue/job for reliability.
        try {
            DB::connection('pgsql')->table('article_vectors')
                ->where('mysql_id', $article->id)
                ->delete();
        } catch (\Exception $e) {
            report($e);
            // Don't block delete, but log the error
        }
        
        $article->delete();
        return response()->json(null, 204);
    }

    // Helper for Tags
    private function syncTags(Article $article, array $tags)
    {
        $tagIds = [];
        foreach ($tags as $tagData) {
            // Check if it's a new tag (e.g., from a 'create-tag' component)
            if (is_array($tagData) && isset($tagData['label'])) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($tagData['label'])],
                    ['name' => $tagData['label']]
                );
                $tagIds[] = $tag->id;
            } elseif (is_numeric($tagData)) {
                $tagIds[] = $tagData;
            }
        }
        $article->tags()->sync($tagIds);
    }

    // List of categories and tags
    public function getEditorData()
    {
        $categories = Category::orderBy('name')->get(['id as value', 'name as label']);
        $tags = Tag::orderBy('name')->get(['id as value', 'name as label']);
        
        return response()->json([
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
