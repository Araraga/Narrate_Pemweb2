<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'article_id',
    ];

    /**
     * Get the user that created the like.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the article that was liked.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}