<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get all articles for the category.
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Get only published articles for the category.
     */
    public function publishedArticles()
    {
        return $this->hasMany(Article::class)->where('status', 'published');
    }

    /**
     * Get the category's article count.
     */
    public function getArticleCountAttribute()
    {
        return $this->articles()->count();
    }

    /**
     * Get the category's published article count.
     */
    public function getPublishedArticleCountAttribute()
    {
        return $this->publishedArticles()->count();
    }
}