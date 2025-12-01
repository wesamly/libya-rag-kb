<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon'];

    /**
     * Set the slug automatically when setting the name.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        if (!isset($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Get the articles for the category.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
