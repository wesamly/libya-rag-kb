<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use Mews\Purifier\Casts\CleanHtmlInput;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'author_id',
        'status',
        'needs_sync',
    ];

    protected function casts(): array
    {
        return [
            'content' => CleanHtmlInput::class,
        ];
    }

    /**
     * Set the slug automatically when setting the title.
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (!isset($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Automatically clean the 'content' field every time it's set.
     */
    // public function setContentAttribute($value)
    // {
    //     $this->attributes['content'] = Purifier::clean($value);
    // }

    /**
     * Get the category that owns the article.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author that wrote the article.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the tags for the article.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tags');
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
