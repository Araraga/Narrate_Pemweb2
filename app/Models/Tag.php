<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
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
    ];

    /**
     * Get all articles for the tag.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }

    /**
     * Get only published articles for the tag.
     */
    public function publishedArticles()
    {
        return $this->belongsToMany(Article::class)
            ->where('status', 'published')
            ->withTimestamps();
    }

    /**
     * Get the tag's article count.
     */
    public function getArticleCountAttribute()
    {
        return $this->articles()->count();
    }
}